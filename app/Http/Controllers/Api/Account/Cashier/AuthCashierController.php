<?php

namespace App\Http\Controllers\Api\Account\Cashier;

use App\Http\Controllers\Controller;
use App\Models\AuthSession;
use App\Models\Organization;
use App\Models\RegistrationSession;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;


class AuthCashierController extends Controller
{

   public function authInit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $account = User::where('phone', normalizePhone($request->phone))->first();
        
        if($account==null){
            return response()->json(['error' => 'Пользователь с таким номером телефона не найден'], 404);
        }
        if($account->role!='cashier' || $account->organizationBranch === null){
            return response()->json(['error' => 'Пользователь с таким номером телефона не привязан к филиалу или не имеет роль соотвествующую'], 404);
        }
        
        $authId = Str::uuid();
        $code = generateRandomNumber();

        AuthSession::create([
            'id' => $authId,
            'user_id' => $account->id,
            'sms_code' => $code,
            'call_code' => generateRandomNumber(), // Генерируем код для звонка
            'sms_sent_at' => now(),
            'call_sent_at' => null,
        ]);

        // Формирование сообщения с кодом подтверждения
        $smsMessage = "<#> {$code} - код подтверждения";

        // Отправка SMS
        $send_sms = sendSms($account->phone, $smsMessage);

        return response()->json([
            'reg_id' => $authId,
            'message' => 'SMS с кодом отправлено'
        ]);
    }

    public function authConfirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reg_id' => 'required|uuid|exists:auth_sessions,id',
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $session = AuthSession::find($request->reg_id);

        if ($session->sms_code !== $request->code) {
            return response()->json(['error' => 'Неверный код'], 400);
        }

        $smsSentAt = \Carbon\Carbon::parse($session->sms_sent_at);
        
        if ($smsSentAt->addMinutes(5)->isPast()) {
            return response()->json(['error' => 'Код истек'], 400);
        }

        // Отправляем звонок с кодом после успешной проверки SMS
        $callSent = sendCode($session->user->phone, $session->call_code);
        
        if ($callSent) {
            $session->update([
                'call_sent_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'SMS код подтвержден. Ожидайте звонок с кодом подтверждения',
                'reg_id' => $session->id,
                'call_sent' => true
            ]);
        } else {
            return response()->json(['error' => 'Ошибка отправки звонка'], 500);
        }
    }

    public function authConfirmCall(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reg_id' => 'required|uuid|exists:auth_sessions,id',
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $session = AuthSession::find($request->reg_id);

        // Проверяем код из звонка
        if ($session->call_code !== $request->code) {
            return response()->json(['error' => 'Неверный код из звонка'], 400);
        }

        // Проверяем срок действия кода из звонка (10 минут)
        $callSentAt = \Carbon\Carbon::parse($session->call_sent_at);
        if ($callSentAt->addMinutes(10)->isPast()) {
            return response()->json(['error' => 'Код из звонка истек'], 400);
        }

        $user = $session->user;
        $token = auth('api')->login($user);

        // Удаляем использованную сессию
        $session->delete();

        // Возвращаем полный ответ с токеном
        return response()->json([
            'success' => true,
            'message' => 'Успешная авторизация',
            'auth_data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
            ],
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                // другие нужные поля пользователя
            ]
        ]);
    }



    
}