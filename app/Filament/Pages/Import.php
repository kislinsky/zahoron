<?php 
namespace App\Filament\Pages;

use App\Services\Parser\ParserOrganizationService;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Http;

class Import extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';
    
    protected static string $view = 'filament.pages.import';
    
    protected static ?string $navigationLabel = 'Импорт организаций';
    
    protected static ?string $title = 'Импорт организаций из файла';
    
    protected static ?int $navigationSort = 3;
    
    public ?array $data = [];
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('files')
                    ->label('Выберите файл')
                    ->multiple()
                    ->required()
                    ->preserveFilenames()
                    ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv']),
                
                Select::make('import_type')
                    ->label('Выберите тип загрузки')
                    ->options([
                        'new' => 'Создать новые организации',
                        'update' => 'Обновить организации',
                    ])
                    ->default('new')
                    ->required()
                    ->live(),
                
                Select::make('import_with_user')
                    ->label('Вкл/Выкл')
                    ->options([
                        0 => 'Нет',
                        1 => 'Да',
                    ])
                    ->default(0)
                    ->required(),
                
                Select::make('columns_to_update')
                    ->label('Выбор полей для обновления данных')
                    ->multiple()
                    ->options([
                        'title' => 'Название организации',
                        'address' => 'Адрес',
                        'coordinates' => 'Координаты',
                        'phone' => 'Телефон',
                        'logo' => 'Логотип',
                        'main_photo' => 'Главное фото',
                        'working_hours' => 'Режим работы',
                        'gallery' => 'Галерея',
                        'services' => 'Виды услуг',
                    ])
                    ->visible(fn (callable $get) => $get('import_type') === 'update'),
            ])
            ->statePath('data');
    }
    
    public function submit()
    {
        $data = $this->form->getState();
        
        try {
            // Проверяем, что files содержит массив объектов UploadedFile
            if (!isset($data['files']) || !is_array($data['files'])) {
                throw new \Exception('Неверный формат файлов для импорта');
            }
            
            // Фильтруем только объекты UploadedFile
            $files = array_filter($data['files'], function ($file) {
                return $file instanceof \Illuminate\Http\UploadedFile;
            });
            
            if (empty($files)) {
                throw new \Exception('Не найдено файлов для импорта');
            }
            
            $result = app(ParserOrganizationService::class)->index([
                'files' => $files, // Теперь точно массив UploadedFile объектов
                'import_type' => $data['import_type'],
                'import_with_user' => $data['import_with_user'],
                'columns_to_update' => $data['columns_to_update'] ?? []
            ]);
            
            Notification::make()
                ->title('Успешно')
                ->body($result['message'])
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Ошибка')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    protected function getFormActions(): array
    {
        return [
            Action::make('submit')
                ->label('Начать импорт')
                ->submit('submit'),
        ];
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
        return auth()->user()->role === 'admin' ;
    }

    public static function canViewAny(): bool
    {
        return static::shouldRegisterNavigation();
    }
}