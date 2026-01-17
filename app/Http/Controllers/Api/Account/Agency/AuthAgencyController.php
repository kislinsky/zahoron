<?php

namespace App\Http\Controllers\Api\Account\Agency;

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
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use session;


class AuthAgencyController extends Controller
{
    
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:Ритуальное агентство,Частный агент,Другое',
            'inn' => 'required|string',
            'phone' => 'required|string|max:15',
            'form' => 'required|in:ООО,ИП,ФЛ',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $phone_check = User::where('phone', normalizePhone($request->phone))->first();
        $inn_check = User::where('inn', $request->inn)->first();

        if ($phone_check != null || $inn_check != null) {
            return response()->json(['error' => 'Пользователь с таким номером телефона или инн уже существует.'], 409);
        }

        // Здесь должна быть проверка ИНН через внешний сервис
        $innInfo = null;
        if (env('API_WORK') == 'true') {
            $innInfo = checkOrganizationInn($request->inn);
        }

        if ($innInfo != null && $innInfo['state']['status'] == 'ACTIVE') {
            $regId = Str::uuid();
            
            RegistrationSession::create([
                'id' => $regId,
                'inn' => $request->inn,
                'phone' => normalizePhone($request->phone),
                'agent_name' => $innInfo['name']['short_with_opf'],
                'status' => $innInfo['state']['status'],
                'okved' => $innInfo['okved'],
            ]);

            // Ищем организации с таким же ИНН в системе
            $organizations = Organization::where('inn', $request->inn)
                ->select('title', 'adres')
                ->get()
                ->map(function ($org) {
                    return [
                        'title' => $org->title,
                        'address' => $org->adres
                    ];
                });

            return response()->json([
                'reg_id' => $regId,
                'agent_name' => $innInfo['name']['short_with_opf'],
                'status' => $innInfo['state']['status'],
                'okved' => $innInfo['okved'],
                'organizations' => $organizations
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


        // Формирование сообщения с кодом подтверждения
        $smsMessage = "<#> {$code} - код подтверждения";

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

        $wallet=Wallet::create(['user_id'=>$account->id]);


        
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


        // Формирование сообщения с кодом подтверждения
        $smsMessage = "<#> {$code} - код подтверждения";

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


    public static function deleteAccountTest(User $user){
        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'Аккаунт успешно удален',
        ]);
    }


    public function sendCode(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
        ]);

        $organization = Organization::find($request->organization_id);
        $code = generateRandomNumber(); // Генерация кода (например, 4-6 цифр)

        // Отправка кода (первый способ)
        $sendCodeResult = sendCode($organization->phone, $code);

        // Если первый способ не сработал, пробуем SMS
        if ($sendCodeResult['tell_code_result']['status'] != 'ok') {
            sendSms($organization->phone, $code);
        }

        // Сохраняем хеш кода в кеш (вместо куки) на 20 минут
        $cacheKey = "verification_code:{$organization->id}";
        Cache::put($cacheKey, Hash::make($code), now()->addMinutes(20));

        return response()->json([
            'success' => true,
            'message' => 'Код отправлен',
        ]);
    }

    public function acceptCode(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'code' => 'required|string|min:4|max:6',
        ]);

        $cacheKey = "verification_code:{$request->organization_id}";
        $hashedCode = Cache::get($cacheKey);

        // Если код не найден или неверный
        if (!$hashedCode || !Hash::check($request->code, $hashedCode)) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный или устаревший код',
            ], 422);
        }

        // Обновляем организацию
        Organization::find($request->organization_id)
            ->update(['user_id' => auth()->id()]);

        // Удаляем код из кеша после успешной проверки
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Код подтвержден, организация привязана',
        ]);
    }

    public static function checkJwtToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Токен обязателен',
                'errors' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }

        try {
            $token = $request->input('token');
            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не найден'
                ], 403); // 403 Forbidden
            }

            // Проверяем срок действия токена
            $payload = JWTAuth::setToken($token)->getPayload();
            $expiration = $payload->get('exp');
            
            if (time() >= $expiration) {
                return response()->json([
                    'success' => false,
                    'message' => 'Токен истек'
                ], 403); // 403 Forbidden
            }

            return response()->json([
                'success' => true,
                'message' => 'Токен действителен',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ],
                'expires_at' => date('Y-m-d H:i:s', $expiration)
            ], 200); // 200 OK

        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Недействительный токен: ' . $e->getMessage()
            ], 403); // 403 Forbidden
        }
    }

    /**
     * Дополнительная функция для проверки токена из заголовка
     */
    public static function checkJwtTokenFromHeader(Request $request)
    {
        try {
            $token = $request->bearerToken();
            
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Токен не предоставлен'
                ], 401); // 401 Unauthorized
            }

            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не найден'
                ], 403); // 403 Forbidden
            }

            return response()->json([
                'success' => true,
                'user' => $user
            ], 200); // 200 OK

        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка проверки токена'
            ], 403); // 403 Forbidden
        }
    }
    
}