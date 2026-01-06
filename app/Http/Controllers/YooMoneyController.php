<?php 
namespace App\Http\Controllers;

use App\Models\Burial;
use App\Models\Edge;
use App\Models\OrderBurial;
use App\Models\OrderService;
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
        Log::info('YooMoney Callback Received', [
            'request_data' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        // Получаем данные из уведомления
        $notificationData = $request->all();
        
        // Проверяем, пришло ли уведомление от ЮKassa
        if ($request->input('type') !== 'notification') {
            throw new \InvalidArgumentException('Неверный тип уведомления');
        }

        $event = $request->input('event');
        $paymentObject = $request->input('object', []);

        if (empty($paymentObject['id'])) {
            throw new \InvalidArgumentException('Не передан идентификатор платежа');
        }

        $paymentId = $paymentObject['id'];
        $paymentStatus = $paymentObject['status'] ?? null;
        $metadata = $paymentObject['metadata'] ?? [];
        $createdAt = $paymentObject['created_at'] ?? null;

        Log::info('Processing payment', [
            'payment_id' => $paymentId,
            'status' => $paymentStatus,
            'event' => $event,
            'metadata' => $metadata
        ]);

        // Обрабатываем только успешные платежи
        if ($event === 'payment.succeeded' && $paymentStatus === 'succeeded') {
            if (isset($metadata['type'])) {
                switch ($metadata['type']) {
                    
                    case 'wallet_update':
                        if (!empty($metadata['wallet_id'])) {
                            $wallet = Wallet::find($metadata['wallet_id']);
                            if ($wallet) {
                                $wallet->deposit($metadata['count'] ?? 0, [], 'Пополнение баланса');
                                Log::info('Wallet updated', ['wallet_id' => $metadata['wallet_id']]);
                            }
                        }
                        break;

                    case 'burial_buy':
                        Log::info('Processing burial purchase', [
                            'user_id' => $metadata['user_id'] ?? null,
                            'burial_id' => $metadata['burial_id'] ?? null,
                            'count' => $metadata['count'] ?? 0
                        ]);

                        // Проверяем наличие обязательных полей
                        if (empty($metadata['user_id']) || empty($metadata['burial_id'])) {
                            Log::error('Missing required fields for burial purchase', $metadata);
                            throw new \InvalidArgumentException('Отсутствуют обязательные поля для покупки захоронения');
                        }

                        // Проверяем, не существует ли уже оплаченного заказа
                        try {
                            $existingOrder = OrderBurial::where('burial_id', $metadata['burial_id'])
                                ->where('user_id', $metadata['user_id'])
                                ->where('status', 1)
                                ->first();
                        } catch (\Exception $e) {
                            Log::error('Error checking existing burial order', [
                                'error' => $e->getMessage(),
                                'metadata' => $metadata
                            ]);
                            $existingOrder = null;
                        }

                        if (!$existingOrder) {
                            // Создаем запись о покупке геолокации
                            OrderBurial::create([
                                'user_id' => $metadata['user_id'],
                                'burial_id' => $metadata['burial_id'],
                                'price' => $metadata['count'] ?? 0,
                                'date_pay' => $createdAt ? date('Y-m-d H:i:s', strtotime($createdAt)) : now(),
                                'status' => 1
                            ]);
                            
                            Log::info('Burial purchase completed', [
                                'user_id' => $metadata['user_id'],
                                'burial_id' => $metadata['burial_id'],
                                'order_created' => true
                            ]);
                        } else {
                            Log::info('Burial already purchased', [
                                'user_id' => $metadata['user_id'],
                                'burial_id' => $metadata['burial_id'],
                                'existing_order_id' => $existingOrder->id
                            ]);
                        }
                        break;

                    case 'services_pay':
                        if (!empty($metadata['order_id'])) {
                            $orderService = OrderService::find($metadata['order_id']);
                            
                            if ($orderService) {
                                // Обновляем заказ услуг
                                $orderService->update([
                                    'paid' => 1,
                                    'date_pay' => $createdAt ? date('Y-m-d H:i:s', strtotime($createdAt)) : now(),
                                    'status' => 2
                                ]);

                                Log::info('Services order updated', ['order_id' => $orderService->id]);

                                // Проверяем, включена ли стоимость геолокации в заказ
                                if (!$orderService->burial_purchased) {
                                    // Получаем стоимость геолокации
                                    $burial = Burial::find($orderService->burial_id);
                                    $burialPrice = $burial->cemetery->price_burial_location ?? 0;
                                    
                                    // Проверяем, оплачена ли уже геолокация для этого пользователя
                                    $isBurialPurchased = OrderBurial::where('burial_id', $orderService->burial_id)
                                        ->where('user_id', $metadata['user_id'])
                                        ->where('status', 1)
                                        ->exists();

                                    // Если геолокация не оплачена и ее стоимость включена в заказ
                                    if (!$isBurialPurchased && $burialPrice > 0) {
                                        // Создаем запись о покупке геолокации
                                        OrderBurial::create([
                                            'user_id' => $metadata['user_id'],
                                            'burial_id' => $orderService->burial_id,
                                            'price' => $burialPrice,
                                            'date_pay' => $createdAt ? date('Y-m-d H:i:s', strtotime($createdAt)) : now(),
                                            'status' => 1
                                        ]);
                                        
                                        Log::info('Burial included in services purchase', [
                                            'order_id' => $orderService->id,
                                            'burial_id' => $orderService->burial_id
                                        ]);
                                    }
                                }
                            }
                        }
                        break;
                        
                    default:
                        Log::warning('Unknown payment type', ['type' => $metadata['type']]);
                        break;
                }
            } else {
                Log::warning('No type in metadata', ['metadata' => $metadata]);
            }
        } else {
            Log::info('Payment not succeeded or wrong event', [
                'event' => $event,
                'status' => $paymentStatus
            ]);
        }

        return response()->json(['status' => 'success']);

    } catch (\Exception $e) {
        Log::error('YooMoney Callback Error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);
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