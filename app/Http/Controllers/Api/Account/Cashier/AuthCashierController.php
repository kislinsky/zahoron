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
            'sms_sent_at' => now(),
        ]);

        $smsMessage = "<#> {$code} - код подтверждения";
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
            'code' => 'required|string|size:4',
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

        $session->delete();

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
            ]
        ]);
    }
}