<?php

namespace App\Filament\Pages;

use App\Jobs\CemeteryImportJob;
use App\Services\Parser\ParserCemeteryService;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Routing\Route;
use Exception;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CemeteryImport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';
    protected static string $view = 'filament.pages.cemetery-import';
    protected static ?string $navigationLabel = 'Импорт кладбищ';
    protected static ?string $title = 'Импорт кладбищ';
    protected static ?int $navigationSort = 5;

    public ?array $data = [];
    public ?array $fileColumns = [];
    public bool $showMapping = false;
    public ?string $jobId = null;
    public bool $isImporting = false;

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
            'import_type' => 'create',
            'columns_to_update' => [],
            'price_geo' => 5900,
        ], $this->data);
    }

    protected array $systemColumns = [
        'id_2gis' => 'ID 2GIS',
        'title' => 'Название кладбища',
        'region' => 'Регион',
        'district' => 'Район',
        'city' => 'Населённый пункт',
        'address' => 'Адрес кладбища',
        'responsible_person_address' => 'Адрес (ответственного лица)',
        'responsible_organization' => 'Ответственная организация',
        'okved' => 'Okved',
        'inn' => 'ИНН',
        'width' => 'Широта',
        'longitude' => 'Долгота',
        'rating' => 'Рейтинг',
        'phone' => 'Телефон',
        'email' => 'Емейл',
        'responsible_person_full_name' => 'Ответственное лицо (ФИО)',
        'cadastral_number' => 'Кадастровый номер',
        'two_gis_link' => 'URL',
        'status' => 'Статус',
        'date_foundation' => 'Дата парсинга',
        'working_hours' => 'Режим работы',
        'photos' => 'Фотографии',
    ];

    protected array $updateableFields = [
        'title' => 'Название',
        'address' => 'Адрес',
        'responsible_person_address' => 'Адрес ответственного лица',
        'responsible_organization' => 'Ответственная организация',
        'okved' => 'ОКВЭД',
        'inn' => 'ИНН',
        'width' => 'Широта',
        'longitude' => 'Долгота',
        'rating' => 'Рейтинг',
        'phone' => 'Телефон',
        'email' => 'Email',
        'responsible_person_full_name' => 'Ответственное лицо (ФИО)',
        'cadastral_number' => 'Кадастровый номер',
        'two_gis_link' => 'Ссылка 2GIS',
        'status' => 'Статус',
        'date_foundation' => 'Дата основания',
        'working_hours' => 'Режим работы',
        'photos' => 'Фотографии',
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
                    ->directory('uploads_cemeteries')
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'text/csv',
                        'text/plain',
                        'application/vnd.ms-excel'
                    ])
                    ->rules(['mimes:csv,xlsx,xls,txt'])
                    ->live()
                    ->afterStateUpdated(function (mixed $state) {
                        $this->processUploadedFile($state);
                    }),

                Radio::make('import_type')
                    ->label('Режим импорта')
                    ->options([
                        'create' => 'Создание новых записей',
                        'update' => 'Обновление существующих записей',
                    ])
                    ->default('create')
                    ->required()
                    ->live()
                    ->visible(fn() => $this->showMapping),

                CheckboxList::make('columns_to_update')
                    ->label('Выберите поля для обновления')
                    ->options($this->updateableFields)
                    ->visible(fn($get) => $get('import_type') === 'update' && $this->showMapping)
                    ->columns(2)
                    ->required(fn($get) => $get('import_type') === 'update')
                    ->validationMessages([
                        'required' => 'Необходимо выбрать хотя бы одно поле для обновления.',
                    ]),

                TextInput::make('price_geo')
                    ->label('Цена за геопозицию (по умолчанию)')
                    ->numeric()
                    ->default(5900)
                    ->visible(fn() => $this->showMapping),

                $this->getMappingSchema(),
            ])
            ->statePath('data');
    }

    protected function getMappingSchema(): Fieldset
    {
        $mappingFields = [];
        $requiredMappingKeys = ['id_2gis', 'title'];

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

    public function processUploadedFile(mixed $file): void
    {
        $this->showMapping = false;
        $this->fileColumns = [];

        try {
            $parser = app(ParserCemeteryService::class);
            $headers = $parser->getFileHeaders($file);

            if (empty($headers)) {
                throw new Exception('Файл пуст или не содержит заголовков.');
            }

            $this->fileColumns = $headers;

            // Автоматический подбор маппинга
            $autoMapping = $this->autoMapColumns($headers);
            $this->data['columnMapping'] = $autoMapping;

            $this->showMapping = true;

            $this->form->fill($this->data, false);

            $mappedCount = count(array_filter($autoMapping));
            $totalCount = count($this->systemColumns);
            $unmappedCount = $totalCount - $mappedCount;
            
            if ($mappedCount > 0) {
                $message = "Автоматически сопоставлено {$mappedCount} из {$totalCount} колонок.";
                if ($unmappedCount > 0) {
                    $message .= " Остальные {$unmappedCount} колонок можно сопоставить вручную.";
                }
                $message .= " Проверьте маппинг и при необходимости скорректируйте его.";
                
                Notification::make()
                    ->title('Заголовки файла успешно извлечены')
                    ->body($message)
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Заголовки файла успешно извлечены')
                    ->body('Не удалось автоматически сопоставить колонки. Выполните маппинг вручную в форме ниже.')
                    ->warning()
                    ->send();
            }

        } catch (Exception $e) {
            Notification::make()
                ->title('Ошибка обработки файла')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Автоматический подбор маппинга колонок файла к системным полям
     *
     * @param array $fileHeaders Заголовки из файла
     * @return array Маппинг системных полей на колонки файла
     */
    protected function autoMapColumns(array $fileHeaders): array
    {
        $mapping = [];
        $usedColumns = [];

        // Варианты названий для каждого системного поля
        $fieldVariants = [
            'id_2gis' => ['id 2gis', 'id_2gis', '2gis id', '2gis', 'id', 'идентификатор', 'object id', 'object_id'],
            'title' => ['название', 'название кладбища', 'title', 'name', 'имя', 'наименование', 'cemetery name'],
            'region' => ['регион', 'region', 'область', 'subject'],
            'district' => ['район', 'district', 'area', 'районный округ'],
            'city' => ['город', 'населённый пункт', 'населенный пункт', 'city', 'town', 'settlement', 'населенный пункт'],
            'address' => ['адрес', 'адрес кладбища', 'address', 'адрес местонахождения'],
            'responsible_person_address' => ['адрес ответственного лица', 'адрес (ответственного лица)', 'responsible person address', 'адрес ответственного'],
            'responsible_organization' => ['ответственная организация', 'responsible organization', 'организация', 'organization'],
            'okved' => ['оквэд', 'okved', 'оквед', 'okved code'],
            'inn' => ['инн', 'inn', 'идентификационный номер налогоплательщика'],
            'width' => ['широта', 'latitude', 'lat', 'координата широта', 'широта (lat)'],
            'longitude' => ['долгота', 'longitude', 'lng', 'lon', 'координата долгота', 'долгота (lng)'],
            'rating' => ['рейтинг', 'rating', 'оценка', 'score'],
            'phone' => ['телефон', 'phone', 'тел', 'контактный телефон', 'телефон контактный'],
            'email' => ['email', 'емейл', 'e-mail', 'почта', 'электронная почта', 'e-mail адрес'],
            'responsible_person_full_name' => ['ответственное лицо', 'ответственное лицо (фио)', 'фио ответственного', 'responsible person', 'fio', 'ф.и.о.'],
            'cadastral_number' => ['кадастровый номер', 'cadastral number', 'кадастр', 'cadastral'],
            'two_gis_link' => ['url', 'ссылка', 'ссылка 2gis', '2gis link', 'link', 'ссылка на 2gis', 'two_gis_link'],
            'status' => ['статус', 'status', 'состояние'],
            'date_foundation' => ['дата парсинга', 'дата основания', 'date', 'дата', 'foundation date', 'parsing date'],
            'working_hours' => ['режим работы', 'working hours', 'часы работы', 'график работы', 'work schedule'],
            'photos' => ['фотографии', 'photos', 'изображения', 'images', 'фото', 'pictures'],
        ];

        // Нормализация строки для сравнения
        $normalize = function ($str) {
            $str = mb_strtolower(trim($str), 'UTF-8');
            // Удаляем лишние пробелы и знаки препинания
            $str = preg_replace('/[^\p{L}\p{N}\s]/u', '', $str);
            $str = preg_replace('/\s+/', ' ', $str);
            return trim($str);
        };

        // Функция для вычисления схожести строк
        $similarity = function ($str1, $str2) {
            $str1 = mb_strtolower(trim($str1), 'UTF-8');
            $str2 = mb_strtolower(trim($str2), 'UTF-8');
            
            // Точное совпадение
            if ($str1 === $str2) {
                return 100;
            }
            
            // Частичное совпадение (одна строка содержит другую)
            if (mb_strpos($str1, $str2) !== false || mb_strpos($str2, $str1) !== false) {
                $minLen = min(mb_strlen($str1), mb_strlen($str2));
                $maxLen = max(mb_strlen($str1), mb_strlen($str2));
                return (int)(($minLen / $maxLen) * 100);
            }
            
            // Используем similar_text для более точного сравнения с UTF-8
            $percent = 0;
            similar_text($str1, $str2, $percent);
            return (int)$percent;
        };

        // Сначала ищем точные совпадения
        foreach ($this->systemColumns as $systemKey => $systemLabel) {
            if (isset($mapping[$systemKey])) {
                continue;
            }

            $normalizedSystemLabel = $normalize($systemLabel);
            $bestMatch = null;
            $bestScore = 0;

            foreach ($fileHeaders as $fileColumn) {
                if (in_array($fileColumn, $usedColumns)) {
                    continue;
                }

                $normalizedFileColumn = $normalize($fileColumn);
                
                // Проверяем точное совпадение с системным названием
                $score = $similarity($normalizedSystemLabel, $normalizedFileColumn);
                
                // Проверяем совпадение с вариантами названий
                if (isset($fieldVariants[$systemKey])) {
                    foreach ($fieldVariants[$systemKey] as $variant) {
                        $normalizedVariant = $normalize($variant);
                        $variantScore = $similarity($normalizedVariant, $normalizedFileColumn);
                        if ($variantScore > $score) {
                            $score = $variantScore;
                        }
                    }
                }

                if ($score > $bestScore && $score >= 60) { // Порог схожести 60%
                    $bestScore = $score;
                    $bestMatch = $fileColumn;
                }
            }

            if ($bestMatch) {
                $mapping[$systemKey] = $bestMatch;
                $usedColumns[] = $bestMatch;
            }
        }

        return $mapping;
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
            $importType = $validatedData['import_type'] ?? 'create';
            $columnsToUpdate = $validatedData['columns_to_update'] ?? [];
            $priceGeo = $validatedData['price_geo'] ?? 5900;

            $file = $validatedData['file'];

            CemeteryImportJob::dispatch(
                $file,
                $mappedData,
                $importType,
                $columnsToUpdate,
                $priceGeo,
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

    public function handleImportFinished(string $status, int $created, int $updated, int $skipped, array $errors): void
    {
        if ($status === 'Ошибка' || !empty($errors)) {
            Notification::make()
                ->title('Импорт завершен с ошибками')
                ->body("Создано: {$created}, Обновлено: {$updated}, Пропущено: {$skipped}")
                ->danger()
                ->persistent()
                ->send();
                
            // Логируем все ошибки для детального анализа
            Log::warning("Импорт кладбищ завершен с ошибками. Всего ошибок: " . count($errors));
            foreach ($errors as $error) {
                Log::info("Ошибка импорта: " . $error);
            }
        } else {
            Notification::make()
                ->title('Импорт успешно завершен!')
                ->body("Создано: {$created}, Обновлено: {$updated}, Пропущено: {$skipped}")
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

