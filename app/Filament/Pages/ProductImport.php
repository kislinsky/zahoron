<?php

namespace App\Filament\Pages;

use App\Jobs\BurialImportJob;
use App\Jobs\ProductImportJob;
use App\Models\Area;
use App\Models\Cemetery;
use App\Models\City;
use App\Models\Edge;
use App\Services\Parser\ParserBurialService;
use App\Services\Parser\ParserProduct;
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

class ProductImport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';
    protected static string $view = 'filament.pages.product-import';
    protected static ?string $navigationLabel = 'Импорт товаров';
    protected static ?string $title = 'Импорт товаров';
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
        ], $this->data);
    }

    protected array $systemColumns = [
        'organization_id' => 'ID организации',
        'title' => 'Название товара',
        'category_parent_title' => 'Категория товара',
        'category_title' => 'Подкатегория товара',
        'content' => 'Описание товара',
        'price' => 'Цена товара',
        'img' => 'Изображение товара',
        'characteristics'=> 'Характеристики товара',
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
                    ->directory('uploads_product')
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
            ])
            ->statePath('data');
    }

    protected function getMappingSchema(): Fieldset
    {
        $mappingFields = [];
        $requiredMappingKeys = ['organization_id', 'category_parent_title', 'category_title'];

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

            $mappingFields[] = $selectField;
        }

        return Fieldset::make('Сопоставление колонок файла')
            ->schema($mappingFields)
            ->reactive()
            ->visible(fn() => $this->showMapping);
    }

    public function processUploadedFile(TemporaryUploadedFile $file): void
    {
        $this->showMapping = false;
        $this->fileColumns = [];

        try {
            $parser = app(ParserProduct::class);
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

            $file = $validatedData['file'];

            ProductImportJob::dispatch(
                $file,
                $mappedData,
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
