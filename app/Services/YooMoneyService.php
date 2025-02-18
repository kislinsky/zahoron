<?php

namespace App\Services;

use YooKassa\Client;
use Exception;
use Illuminate\Support\Facades\Log;

class YooMoneyService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuth(env('SHOP_ID_YOOMONEY'), env('API_YOOMONEY')); // Замените на свои данные
    }

    /**
     * Создает платеж в YooMoney.
     *
     * @param float $amount Сумма платежа.
     * @param string $description Описание платежа.
     * @param string $returnUrl URL для возврата после оплаты.
     * @return array Возвращает массив с результатом и информацией о платеже.
     */
    public function createPayment(float $amount, string $description, string $returnUrl): array
    {
        try {
            // Параметры платежа
            $payment = $this->client->createPayment(
                [
                    'amount' => [
                        'value' => number_format($amount, 2, '.', ''), // Форматируем сумму
                        'currency' => 'RUB', // Валюта
                    ],
                    'confirmation' => [
                        'type' => 'redirect',
                        'return_url' => $returnUrl, // URL для возврата после оплаты
                    ],
                    'capture' => true,
                    'description' => $description, // Описание платежа
                ],
                uniqid('', true) // Уникальный идентификатор платежа
            );

            // Возвращаем успешный результат и информацию о платеже
            return [
                'success' => true,
                'payment' => [
                    'id' => $payment->getId(),
                    'status' => $payment->getStatus(),
                    'redirect_url' => $payment->getConfirmation()->getConfirmationUrl(), // Убедитесь, что этот ключ присутствует
                    'confirmation_url' => $payment->getConfirmation()->getConfirmationUrl(),
                ],
            ];

        } catch (Exception $e) {
            // Логируем ошибку
            Log::error('YooMoney payment error: ' . $e->getMessage());

            // Возвращаем ошибку и информацию
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'payment' => null,
            ];
        }
    }

    /**
     * Обрабатывает callback от YooMoney.
     *
     * @param string $paymentId Идентификатор платежа.
     * @return array Возвращает массив с результатом и информацией о платеже.
     */
    public function handleCallback(string $paymentId): array
    {
        try {
            // Получаем информацию о платеже
            $payment = $this->client->getPaymentInfo($paymentId);

            // Проверяем статус платежа
            if ($payment->getStatus() === 'succeeded') {
                // Платеж успешен
                return [
                    'success' => true,
                    'payment' => [
                        'id' => $payment->getId(),
                        'status' => $payment->getStatus(),
                        'amount' => $payment->getAmount(),
                    ],
                ];
            } else {
                // Платеж не удался
                return [
                    'success' => false,
                    'payment' => [
                        'id' => $payment->getId(),
                        'status' => $payment->getStatus(),
                        'amount' => $payment->getAmount(),
                    ],
                ];
            }

        } catch (Exception $e) {
            // Логируем ошибку
            Log::error('YooMoney callback error: ' . $e->getMessage());

            // Возвращаем ошибку и информацию
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'payment' => null,
            ];
        }
    }
}