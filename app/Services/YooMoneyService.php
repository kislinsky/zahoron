<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use YooKassa\Client;

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
    
    public function createPayment($value,$redirect_url='https://zahoron.ru/elizovo',$description,$metadata=[])
    {

        // Параметры платежа
        $payment = $this->client->createPayment(
        [
            'amount' => [
                'value' => $value, // Сумма платежа
                'currency' => 'RUB', // Валюта
            ],
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => $redirect_url, // URL для возврата после оплаты
            ],
            'metadata' => $metadata,
            'capture' => true,
            'description' => $description, // Описание платежа
        ],
        uniqid('', true) // Уникальный идентификатор платежа
    );

        // Перенаправляем пользователя на страницу оплаты
        return redirect($payment->getConfirmation()->getConfirmationUrl());
    }

    /**
     * Обрабатывает callback от YooMoney.
     *
     * @param string $paymentId Идентификатор платежа.
     * @return array Возвращает массив с результатом и информацией о платеже.
     */
    public function handleCallback(Request $request)
    {
        try {
            $paymentId = $request->input('object.id') ?? $request->input('paymentId');
            
            if (empty($paymentId)) {
                throw new \InvalidArgumentException('Не передан идентификатор платежа');
            }

            $payment = $this->client->getPaymentInfo($paymentId);

            return response()->json([
                'status' => $payment->getStatus(),
                'payment' => [
                    'id' => $payment->getId(),
                    'status' => $payment->getStatus(),
                    'amount' => $payment->getAmount()->getValue(),
                    'currency' => $payment->getAmount()->getCurrency(),
                    'description' => $payment->getDescription(),
                    'metadata' => $payment->getMetadata(),
                    'created_at' => $payment->getCreatedAt()->format('Y-m-d H:i:s'),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('YooMoney Callback Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Проверяет статус платежа
     * 
     * @param string $paymentId
     * @return array
     */
    public function checkPaymentStatus(string $paymentId): array
    {
        try {
            $payment = $this->client->getPaymentInfo($paymentId);
            
            return [
                'status' => $payment->getStatus(),
                'paid' => $payment->getPaid(),
                'amount' => $payment->getAmount()->getValue(),
                'currency' => $payment->getAmount()->getCurrency(),
            ];
        } catch (\Exception $e) {
            Log::error('YooMoney Check Status Error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }


 
    public function createMobilePayment($amount, $deepLink, $description, $metadata = [])
    {
        $payment = $this->client->createPayment([
            'amount' => [
                'value' => $amount,
                'currency' => 'RUB',
            ],
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => $deepLink, // Deep link для возврата в приложение
            ],
            'metadata' => $metadata,
            'capture' => true,
            'description' => $description,
        ], uniqid('', true));

        return [
            'id' => $payment->getId(),
            'status' => $payment->getStatus(),
            'confirmation_url' => $payment->getConfirmation()->getConfirmationUrl(),
            'amount' => $payment->getAmount()->getValue()
        ];
    }
}