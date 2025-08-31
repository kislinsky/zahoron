<?php
namespace App\Services\Auth;

use Illuminate\Support\Facades\Http;

class SmsService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('smsru.key'); // Берем API-ключ из конфигурации
    }

    /**
     * Отправка SMS
     *
     * @param string $to Номер телефона
     * @param string $message Сообщение
     * @return array
     */
    public function sendSms(string $to, string $message): array
    {
         $appHash = 'Zc5zArH2ZXK';

        $response = Http::get('https://sms.ru/sms/send', [
            'api_id' => $this->apiKey,
            'to' => normalizePhone($to), // Номер телефона в формате 79876543210
            'msg' => $message."\n{$appHash}", // Сообщение
            'json' => 1, // Ответ в формате JSON
        ]);

        // Возвращаем обработанный результат
        if ($response->ok()) {
            return $response->json();
        }

        return [
            'status' => 'error',
            'message' => 'Не удалось отправить запрос',
        ];
    }
}