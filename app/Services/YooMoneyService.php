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
    
  public function createPayment($value, $redirect_url = 'https://zahoron.ru/elizovo', $description, $metadata = [], $customerEmail = null, $customerPhone = null,$redirect=true)
{
    // Базовые параметры платежа
    $paymentData = [
        'amount' => [
            'value' => $value,
            'currency' => 'RUB',
        ],
        'confirmation' => [
            'type' => 'redirect',
            'return_url' => $redirect_url,
        ],
        'metadata' => $metadata,
        'capture' => true,
        'description' => $description,
        'receipt' => [
            'customer' => [],
            'items' => [
                [
                    'description' => $description, // Название товара/услуги
                    'quantity' => 1, // Количество
                    'amount' => [
                        'value' => $value,
                        'currency' => 'RUB'
                    ],
                    'vat_code' => 1, // Ставка НДС (1 = 20%, 2 = 10%, 3 = 0%, 4 = без НДС, 5 = 20/120, 6 = 10/110)
                    'payment_mode' => 'full_payment', // Полный расчет
                    'payment_subject' => 'service' // Услуга (или 'commodity' для товара)
                ]
            ]
        ]
    ];

    // Добавляем контактные данные покупателя (email или телефон - обязательно)
    if ($customerEmail) {
        $paymentData['receipt']['customer']['email'] = $customerEmail;
    } elseif ($customerPhone) {
        $paymentData['receipt']['customer']['phone'] = $customerPhone;
    } else {
        // Если нет контактов - убираем чек (но лучше всегда передавать контакт)
        unset($paymentData['receipt']);
    }

    // Создаем платеж
    $payment = $this->client->createPayment(
        $paymentData,
        uniqid('', true)
    );

    if($redirect!=true){
        return $payment->getConfirmation()->getConfirmationUrl();
    }
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


 
    public function createMobilePayment($amount, $deepLink, $description, $metadata = [], $customerEmail = null, $customerPhone = null)
    {
        // Базовые параметры платежа
        $paymentData = [
            'amount' => [
                'value' => $amount,
                'currency' => 'RUB',
            ],
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => $deepLink,
            ],
            'metadata' => $metadata,
            'capture' => true,
            'description' => $description,
        ];
        
        // Добавляем чек, если есть email или телефон
        if ($customerEmail || $customerPhone) {
            $paymentData['receipt'] = [
                'customer' => [],
                'items' => [
                    [
                        'description' => $description,
                        'quantity' => '1.00', // Важно: строка с двумя знаками
                        'amount' => [
                            'value' => $amount,
                            'currency' => 'RUB'
                        ],
                        'vat_code' => 1, // НДС 20%
                        'payment_mode' => 'full_payment',
                        'payment_subject' => 'service'
                    ]
                ]
            ];
            
            if ($customerEmail) {
                $paymentData['receipt']['customer']['email'] = $customerEmail;
            }
            if ($customerPhone) {
                $paymentData['receipt']['customer']['phone'] = $customerPhone;
            }
        }

        // Создаем платеж
        $payment = $this->client->createPayment(
            $paymentData,
            uniqid('', true)
        );

        return [
            'id' => $payment->getId(),
            'status' => $payment->getStatus(),
            'confirmation_url' => $payment->getConfirmation()->getConfirmationUrl(),
            'amount' => $payment->getAmount()->getValue()
        ];
    }
}