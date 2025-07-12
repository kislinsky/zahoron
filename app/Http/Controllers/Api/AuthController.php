<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuthSession;
use App\Models\RegistrationSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use session;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:Ритуальное агенство,Частный агент,Другое',
            'inn' => 'required|string|size:12',
            'phone' => 'required|string|max:15',
            'form' => 'required|in:ООО,ИП,ФЛ',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }


        $phone_check=User::where('phone',normalizePhone($request->phone))->first();
        $inn_check=User::where('inn',$request->inn)->first();

        if($phone_check!=null || $inn_check!=null){
            return response()->json(['error' => 'Пользователь с таким номером телефона или инн уже существует.'], 409);
        }

        // Здесь должна быть проверка ИНН через внешний сервис
        $innInfo=null;
        if(env('API_WORK')=='true'){
            $innInfo=checkOrganizationInn($request->inn);
        }

        
        
        if($innInfo!=null && $innInfo['state']['status']=='ACTIVE'){
            $regId = Str::uuid();
            RegistrationSession::create([
                'id' => $regId,
                'inn' => $request->inn,
                'phone' => normalizePhone($request->phone),
                'agent_name' => $innInfo['name']['short_with_opf'],
                'status' => $innInfo['state']['status'],
                'okved' => $innInfo['okved'],
            ]);

            return response()->json([
                'reg_id' => $regId,
                'agent_name' => $innInfo['name']['short_with_opf'],
                'status' => $innInfo['state']['status'],
                'okved' => $innInfo['okved'],
            ]);
        }
        return response()->json(['error' => 'ИНН не найден или неверный'], 404);

    }

    public function confirmInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reg_id' => 'required|uuid|exists:registration_sessions,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $session = RegistrationSession::find($request->reg_id);
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        $session->update([
            'sms_code' => $code,
            'sms_sent_at' => now(),
        ]);

        $appHash = 'XhczEZL+G64';

        // Формирование сообщения с кодом подтверждения
        $smsMessage = "<#> {$code} - код подтверждения\n{$appHash}";

        // Отправка SMS
        $send_sms = sendSms($session->phone, $smsMessage);

        return response()->json(['message' => 'SMS отправлен']);
    }

    public function confirmPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reg_id' => 'required|uuid|exists:registration_sessions,id',
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $session = RegistrationSession::find($request->reg_id);

        if ($session->sms_code !== $request->code) {
            return response()->json(['error' => 'Неверный код'], 400);
        }

        $smsSentAt = \Carbon\Carbon::parse($session->sms_sent_at);

        if ($smsSentAt->addMinutes(5)->isPast()) {
            return response()->json(['error' => 'Код истек'], 400);
        }

        $form='ep';
        if($session->form=='ООО'){
            $form='organization';
        }
        // Создаем аккаунт
        $account = User::create([
            'role' => $session->type ?? 'organization',
            'inn' => $session->inn,
            'phone' => $session->phone,
            'form' =>$form,
            'okved' => $session->okved,
        ]);


        
        $token = JWTAuth::fromUser($account);

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $account,
            'token' => $this->respondWithToken($token)
        ], 201);
    }


    protected function respondWithToken($token){
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60, // Используем JWTAuth фасад
            'user' => auth('api')->user()
        ];
    }

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
        
        $authId = Str::uuid();
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        AuthSession::create([
            'id' => $authId,
            'user_id' => $account->id,
            'sms_code' => $code,
            'sms_sent_at' => now(),
        ]);

      $appHash = 'XhczEZL+G64';

        // Формирование сообщения с кодом подтверждения
        $smsMessage = "<#> {$code} - код подтверждения\n{$appHash}";

        // Отправка SMS
        $send_sms = sendSms($account->phone, $smsMessage);

        return response()->json([
            'reg_id' => $authId,
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