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
            $paymentId = $request->input('object.id') ?? $request->input('paymentId');
            
            if (empty($paymentId)) {
                throw new \InvalidArgumentException('Не передан идентификатор платежа');
            }

            $payment = $this->client->getPaymentInfo($paymentId);

            $metadata=$payment->getMetadata();
            if($payment->getStatus()=='succeeded'){

                if(isset($metadata['type'])){

                    if( $metadata['type']=='wallet_update'){
                        $wallet=Wallet::find($metadata['wallet_id']);
                        $wallet->deposit($metadata['count'],[],'Пополнение баланса');
                    }

                   elseif( $metadata['type']=='burial_buy'){
                        // Создаем запись о покупке геолокации
                        OrderBurial::create([
                            'user_id' => $metadata['user_id'],
                            'burial_id' => $metadata['order_id'],
                            'price' => $metadata['count'],
                            'date_pay' => $payment->getCreatedAt()->format('Y-m-d H:i:s')
                        ]);

                        // Получаем информацию о заказе
                        $orderService = OrderService::find($metadata['order_id']);
                        if ($orderService) {
                            $burialId = $orderService->burial_id;
                            $userId = $metadata['user_id'];
                            
                            // Получаем стоимость геолокации
                            $burial = Burial::find($burialId);
                            $burialPrice = $burial->cemetery->price_burial_location ?? 0;

                            // Находим все неоплаченные заказы услуг этого пользователя для этого захоронения
                            // которые были созданы ДО момента оплаты геолокации
                            $unpaidOrders = OrderService::where('user_id', $userId)
                                ->where('burial_id', $burialId)
                                ->where('status', 0)
                                ->where('created_at', '<=', $payment->getCreatedAt()->format('Y-m-d H:i:s'))
                                ->get();

                            foreach ($unpaidOrders as $unpaidOrder) {
                                // Вычитаем только если в заказе изначально была включена стоимость геолокации
                                if (!$unpaidOrder->burial_purchased && $unpaidOrder->price > $burialPrice) {
                                    $servicesPrice = $unpaidOrder->price - $burialPrice;
                                    $unpaidOrder->update([
                                        'price' => max(0, $servicesPrice),
                                        'burial_purchased' => true
                                    ]);
                                }
                            }

                            // Обновляем статус текущего заказа
                            $orderService->update(['status' => 1]);
                        }
                    }
                    elseif( $metadata['type']=='services_pay'){
                        // Обновляем заказ услуг
                        $orderService = OrderService::find($metadata['order_id']);
                        if ($orderService) {
                            $orderService->update([
                                'paid' => 1,
                                'date_pay' => $payment->getCreatedAt()->format('Y-m-d H:i:s'),
                                'status' => 2 // или другой статус для оплаченных услуг
                            ]);
                        }

                        // Проверяем, была ли уже куплена геолокация для этого захоронения
                        $isBurialPurchased = OrderBurial::where('burial_id', $orderService->burial_id)
                            ->where('user_id', $metadata['user_id'])
                            ->where('status', 1)
                            ->exists();

                        // Если геолокация не была оплачена, создаем или обновляем заказ геолокации
                        if (!$isBurialPurchased) {
                            // Проверяем, существует ли уже запись о геолокации (но не оплачена)
                            $existingOrderBurial = OrderBurial::where('burial_id', $orderService->burial_id)
                                ->where('user_id', $metadata['user_id'])
                                ->first();

                            if ($existingOrderBurial) {
                                // Обновляем существующую запись
                                $existingOrderBurial->update([
                                    'status' => 1,
                                    'paid' => 1,
                                    'date_pay' => $payment->getCreatedAt()->format('Y-m-d H:i:s'),
                                    'price' => $metadata['original_price'] - $metadata['count'] // стоимость геолокации
                                ]);
                            } else {
                                // Создаем новую запись о покупке геолокации
                                OrderBurial::create([
                                    'user_id' => $metadata['user_id'],
                                    'burial_id' => $orderService->burial_id,
                                    'price' => $metadata['original_price'] - $metadata['count'], // стоимость геолокации
                                    'date_pay' => $payment->getCreatedAt()->format('Y-m-d H:i:s'),
                                    'status' => 1,
                                    'paid' => 1
                                ]);
                            }
                        }
                    }
                }
                
            }

        

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