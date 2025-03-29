<?php 
namespace App\Http\Controllers;

use App\Models\Edge;
use Illuminate\Http\Request;
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
        Edge::create([
            'title'=>'fewrf',
        ]);
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

    public function success(Request $request)
    {
        // Страница успешной оплаты
        dd('Готово');
    }
}