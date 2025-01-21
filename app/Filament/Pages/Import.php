<?php 
namespace App\Filament\Pages;

use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;

class Import extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text'; // Иконка в меню
    protected static string $view = 'filament.pages.import'; // Шаблон страницы
    protected static ?string $navigationLabel = 'Импорт файла'; // Название в меню

    // Свойство для хранения файла
    public ?UploadedFile $file = null;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('file')
                    ->label('Загрузите файл')
                    ->required()
                    ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']) // Разрешенные типы файлов
                    ->maxSize(1024) // Максимальный размер файла в килобайтах
                    ->preserveFilenames(), // Сохранять оригинальные имена файлов
            ]);
    }

    public function submit(): void
    {
        // Получаем данные формы
        $data = $this->form->getState();

        // Получаем загруженный файл
        $this->file = $data['file'];

        // Отправляем файл на кастомный маршрут
        $response = $this->sendFileToCustomRoute($this->file);

        // Уведомление об успешной отправке
        if ($response->successful()) {
            Notification::make()
                ->title('Файл успешно отправлен на обработку')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Ошибка при отправке файла')
                ->danger()
                ->send();
        }
    }

    protected function sendFileToCustomRoute(UploadedFile $file)
    {
        // Используем HTTP-клиент для отправки файла на кастомный маршрут
        return Http::attach(
            'file', // Имя поля для файла
            file_get_contents($file->getRealPath()), // Содержимое файла
            $file->getClientOriginalName() // Имя файла
        )->post(route('custom.import.file'));
    }
}