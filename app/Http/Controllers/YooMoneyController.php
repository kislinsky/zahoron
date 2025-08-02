<?php 
namespace App\Http\Controllers;

use App\Models\Edge;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use YooKassa\Client;
use YooKassa\Model\Notification\NotificationEventType;

class YooMoneyController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuth('1033776', 'test_yPCghkSj8e6PYV4z89CHLQz7NCk4UgcnnQe8iLeb-gQ'); // Замените на свои данные
    }

    public function createPayment(Request $request)
    {

        // Параметры платежа
        $payment = $this->client->createPayment(
            [
                'amount' => [
                    'value' => '2.00', // Сумма платежа
                    'currency' => 'RUB', // Валюта
                ],
                'confirmation' => [
                    'type' => 'redirect',
                    'return_url' => 'https://zahoron.ru/elizovo', // URL для возврата после оплаты
                ],
                'capture' => true,
                'description' => 'Пробный платеж', // Описание платежа
            ],
            uniqid('', true) // Уникальный идентификатор платежа
        );

        // Перенаправляем пользователя на страницу оплаты
        return redirect($payment->getConfirmation()->getConfirmationUrl());
    }

   public function handleCallback(Request $request)
    {
        try {
            $paymentId = $request->input('object.id') ?? $request->input('paymentId');
            
            if (empty($paymentId)) {
                throw new \InvalidArgumentException('Не передан идентификатор платежа');
            }

            $payment = $this->client->getPaymentInfo($paymentId);

            $metadata=$payment->getMetadata();
            $wallet=Wallet::find($metadata['wallet_id']);
            $wallet->deposit($metadata['count'],[],'Пополнение баланса');

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

    public function success(Request $request)
    {
        // Страница успешной оплаты
        dd('Готово');
    }
}