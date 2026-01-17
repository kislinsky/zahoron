<?php

namespace App\Filament\Pages;

use App\Jobs\BurialImportJob;
use App\Models\Area;
use App\Models\Cemetery;
use App\Models\City;
use App\Models\Edge;
use App\Services\Parser\ParserBurialService;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Route;
use Exception;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BurialImport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';
    protected static string $view = 'filament.pages.burial-import';
    protected static ?string $navigationLabel = 'Импорт захоронений';
    protected static ?string $title = 'Импорт захоронений';
    protected static ?int $navigationSort = 4;


    public ?array $data = []; // Будет содержать все поля формы
    public ?array $fileColumns = []; // Будет содержать список колонок из файла
    public bool $showMapping = false;  // Отвечает за отображения блока маппинга
    public ?string $jobId = null; // Уникальный идентификатор Job
    public bool $isImporting = false; // Индикатор импорта

    protected $listeners = [
        'importFinished' => 'handleImportFinished',
        'refresh' => '$refresh',
        'refreshPage' => 'handleRefreshPage'
    ];

    public function mount(): void
    {
        $this->form->fill();

        $this->data = array_merge([
            'file' => null,
            'columnMapping' => [],
            'edge_id' => null,
            'area_id' => null,
            'city_id' => null,
            'selectedCemeteryId' => null,
        ], $this->data);
    }

    protected array $systemColumns = [
        'surname' => 'Фамилия',
        'name' => 'Имя',
        'patronymic' => 'Отчество',
        'date_birth' => 'Дата рождения',
        'date_death' => 'Дата смерти',
        'img_url' => 'Изображение (URL)',
        'img_original_url' => 'Оригинальное изображение (URL)',
        'city'=> 'Город',
        'width' => 'Широта',
        'longitude' => 'Долгота',
        'cemetery_column' => 'Кладбище',
    ];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('file')
                    ->label('Выберите файл для импорта')
                    ->required()
                    ->preserveFilenames()
                    ->disk('public')
                    ->directory('uploads_burials')
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'text/csv',
                        'text/plain',
                        'application/vnd.ms-excel'
                    ])
                    ->rules(['mimes:csv,xlsx,xls,txt'])
                    ->live()
                    ->maxSize(102400)
                    ->afterStateUpdated(function (mixed $state) {
                        $this->processUploadedFile($state);
                    }),

                $this->getMappingSchema(),

                $this->getLocationSchema(),
            ])
            ->statePath('data');
    }

    // Схема выбора местоположения
    protected function getLocationSchema(): Fieldset
    {
        return Fieldset::make('Выбор кладбища по умолчанию')
            ->schema([
                Select::make('edge_id')
                    ->label('Край')
                    ->options(Edge::all()->pluck('title', 'id'))
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('area_id', null);
                        $set('city_id', null);
                        $set('selectedCemeteryId', null);
                    })
                    ->required()
                    ->validationMessages([
                        'required' => 'Поле Край обязательно для выбора, так как вы не выбрали соответствие для Кладбища в excel файле.',
                    ]),

                Select::make('area_id')
                    ->label('Округ')
                    ->options(fn ($get) => Area::where('edge_id', $get('edge_id') ?? 0)->pluck('title', 'id'))
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('city_id', null);
                        $set('selectedCemeteryId', null);
                    })
                    ->required()
                    ->validationMessages([
                        'required' => 'Поле Округ обязательно для выбора, так как вы не выбрали соответствие для Кладбища в excel файле.',
                    ]),

                Select::make('city_id')
                    ->label('Город')
                    ->options(fn ($get) => City::where('area_id', $get('area_id') ?? 0)->pluck('title', 'id'))
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('selectedCemeteryId', null);
                    })
                    ->required()
                    ->validationMessages([
                        'required' => 'Поле Город обязательно для выбора, так как вы не выбрали соответствие для Кладбища в excel файле.',
                    ]),

                Select::make('selectedCemeteryId')
                    ->label('Кладбище')
                    ->options(fn ($get) => Cemetery::where('city_id', $get('city_id') ?? 0)->pluck('title', 'id'))
                    ->searchable()
                    ->required(function () {
                        return empty($this->data['columnMapping']['cemetery_column'] ?? null);
                    })
                    ->validationMessages([
                        'required' => 'Поле "Кладбище" обязательно для выбора, так как вы не выбрали для него соответствие в excel файле.',
                    ]),
            ])
            ->columns(2)
            ->visible(fn() => $this->showMapping && empty($this->data['columnMapping']['cemetery_column'] ?? null));
    }

    protected function getMappingSchema(): Fieldset
    {
        $mappingFields = [];
        $requiredMappingKeys = ['surname', 'name', 'date_birth', 'date_death', 'city', 'width', 'longitude'];

        foreach ($this->systemColumns as $key => $label) {
            $isRequired = in_array($key, $requiredMappingKeys);

            $selectField = Select::make("columnMapping.{$key}")
                ->label($label . ' (колонка в файле)')
                ->placeholder('Выберите колонку')
                ->options(function () {
                    $options = [];
                    $fileColumns = $this->fileColumns;
                    foreach ($fileColumns as $column) {
                        $options[$column] = $column;
                    }
                    return $options;
                })
                ->reactive();

            if ($isRequired) {
                $selectField->required();
            }

            if ($key === 'cemetery_column') {
                $selectField->afterStateUpdated(function ($state, $component) {
                    $this->data['columnMapping']['cemetery_column'] = $state;
                    $this->dispatch('refresh');
                });
            }

            $mappingFields[] = $selectField;
        }

        return Fieldset::make('Сопоставление колонок файла')
            ->schema($mappingFields)
            ->reactive()
            ->visible(fn() => $this->showMapping);
    }

    public function processUploadedFile(mixed $file): void
    {
        $this->showMapping = false;
        $this->fileColumns = [];

        try {
            $parser = app(ParserBurialService::class);
            $headers = $parser->getFileHeaders($file);

            if (empty($headers)) {
                throw new Exception('Файл пуст или не содержит заголовков.');
            }

            $this->fileColumns = $headers;

            $this->showMapping = true;

            $this->form->fill($this->data, false);

            Notification::make()
                ->title('Заголовки файла успешно извлечены. Выполните маппинг вручную.')
                ->success()
                ->send();

        } catch (Exception $e) {
            Notification::make()
                ->title('Ошибка обработки файла')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('startImport')
                ->label('Подтвердить маппинг и начать импорт')
                ->color('success')
                ->visible(fn() => $this->showMapping && !$this->isImporting)
                ->action('handleImport'),
        ];
    }

    public function handleImport(): void
    {
        try {
            $validatedData = $this->form->getState();

            $this->jobId = (string) Str::uuid();
            $this->isImporting = true;
            $this->showMapping = false;

            $mappedData = $validatedData['columnMapping'];
            $selectedCemeteryId = $validatedData['selectedCemeteryId'] ?? null;

            $file = $validatedData['file'];

            BurialImportJob::dispatch(
                $file,
                $mappedData,
                $selectedCemeteryId,
                $this->jobId
            );

            $this->dispatch('refresh');

        } catch (ValidationException $e) {
            $this->isImporting = false;
            $this->showMapping = true;
            throw $e;
        } catch (Exception $e) {
            $this->isImporting = false;
            $this->showMapping = true;
            Notification::make()
                ->title('Критическая ошибка импорта')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function handleImportFinished(string $status, int $created, int $skipped, array $errors): void
    {
        if ($status === 'Ошибка' || !empty($errors)) {
            Notification::make()
                ->title('Импорт завершен с ошибками')
                ->body("Создано: {$created}, Пропущено: {$skipped}, Ошибок: " . count($errors))
                ->danger()
                ->persistent()
                ->send();
        } else {
            Notification::make()
                ->title('Импорт успешно завершен!')
                ->body("Создано: {$created}, Пропущено: {$skipped}")
                ->success()
                ->send();
        }
    }

    public function handleRefreshPage(): void
    {
        $this->isImporting = false;
        $this->jobId = null;
        $this->form->fill();
        $this->showMapping = false;
        $this->fileColumns = [];
    }

    public static function getRoutes(): array
    {
        return [
            Route::get('/', static::class)
                ->name(static::getSlug()),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public static function canViewAny(): bool
    {
        return static::shouldRegisterNavigation();
    }
}
