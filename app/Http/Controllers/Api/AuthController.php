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
            return response()->json(['error' => 'Пользователь с таким номером телефона или инн уже существует.'], 404);
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

        // Здесь должен быть вызов сервиса отправки SMS
        $send_sms=sendSms($session->phone,"Ваш код подтверждения: $code");

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

        // Генерируем JWT токен
        $token = auth('api')->login($account);

        return response()->json([
            'jwt' => $token,
        ]);
    }

    public function authInit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|exists:users,phone',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $account = User::where('phone', normalizePhone($request->phone))->first();
        $authId = Str::uuid();
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        AuthSession::create([
            'id' => $authId,
            'user_id' => $account->id,
            'sms_code' => $code,
            'sms_sent_at' => now(),
        ]);

        // Здесь должен быть вызов сервиса отправки SMS
        $send_sms=sendSms($account->phone,"Ваш код подтверждения: $code");

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

        return response()->json([
            'jwt' => $token,
        ]);
    }

    
}