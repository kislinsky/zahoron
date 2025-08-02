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
      
        // Обработка уведомления от YooMoney
        $paymentId = $request->input('paymentId');
        $payment = $this->client->getPaymentInfo($paymentId);

        if ($payment->getStatus() === 'succeeded') {
            // Платеж успешен
            return response()->json(['status' => 'success', 'payment' => $payment]);
        } else {
            // Платеж не удался
            return response()->json(['status' => 'failed', 'payment' => $payment]);
        }
    }
}