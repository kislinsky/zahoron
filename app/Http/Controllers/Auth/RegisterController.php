<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCodes;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Organization;
use App\Providers\RouteServiceProvider;
use App\Rules\RecaptchaRule;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
        $this->redirectTo = '/' . selectCity()->slug . '/home';
    }

    protected function validator(array $data)
    {
        if ($data['role'] === 'user') {
            return Validator::make($data, [
                'g-recaptcha-response' => ['required', new RecaptchaRule],
                'role'     => ['required', 'string'],
                'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);
        }
    
        return Validator::make($data, [
            'g-recaptcha-response' => ['required', new RecaptchaRule],
            'role'              => ['required', 'string'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'          => ['required', 'string', 'min:8', 'confirmed'],
            'inn'               => ['required', 'string','unique:users'],
            'organization_form' => ['required', 'string'],
        ]);
    }
    
    public function register(Request $request)
    {
        $data = $request->all();
        
        $validator = $this->validator($data);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        if ($data['role'] === 'user') {
            $user = $this->create($data);
            Wallet::create(['user_id'=>$user->id]);
            Auth::login($user);
            return redirect()->route('home');
        }

        $inn_user = User::where('inn', $data['inn'])->get();
        if($inn_user->count() > 0){
            return redirect()->back()->with('error', 'Пользователь с таким ИНН уже существует.');
        }

        $organization_data = null;
        if(env('API_WORK') == 'true'){
            $organization_data = checkOrganizationInn($data['inn']);
        }

        if($organization_data != null && $organization_data['state']['status'] == 'ACTIVE'){
            return redirect()->route('confirm.inn.information.email')->with([
                'email'             => $data['email'],
                'role'              => $data['role'],
                'password'          => Hash::make($data['password']),
                'inn'               => $data['inn'],
                'organization_form' => $data['organization_form'],
                'contragent'        => '3edqwe',
                'status'            => 'Действующий',
                'okved'             => $organization_data['okved'],
            ]);
        }
        
        return redirect()->back()->with('error', 'Такого ИНН не существует или он не действителен');
    }
    
    protected function create(array $data): User
    {
        return User::create([
            'role'     => $data['role'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public static function confirmInnInformationEmail()
    {
        $email = session('email');
        $inn = session('inn');
        $contragent = session('contragent');
        $status = session('status');
        $okved = session('okved');
        $role = session('role');
        $password = session('password');
        $organization_form = session('organization_form');
        
        $existingOrganizations = Organization::where('inn', $inn)
            ->get(['id', 'title', 'email', 'phone', 'created_at','slug']);
        
        return view('auth.confirm-inn-information-email', compact(
            'password', 
            'organization_form', 
            'role', 
            'email', 
            'inn', 
            'contragent', 
            'status', 
            'okved',
            'existingOrganizations'
        ));
    }

    public static function createUserOrganizationEmail(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
            'inn' => ['required', 'string'],
            'contragent' => ['required', 'string'],
            'status' => ['required', 'string'],
            'okved' => ['required', 'string'],
            'organization_form' => ['required', 'string'],
            'role' => ['required', 'string'],
            'organizations' => ['nullable', 'array'],
            'organizations.*' => ['nullable', 'integer'],
        ]);

        $user = User::create([
            'email' => $data['email'],
            'password' => $data['password'],
            'inn' => $data['inn'],
            'organization_form' => $data['organization_form'],
            'role' => $data['role'],
        ]);

        Wallet::create(['user_id' => $user->id]);

        if (!empty($data['organizations'])) {
            Organization::whereIn('id', $data['organizations'])
                ->update(['user_id' => $user->id]);
        }

        Auth::login($user);
        return redirect()->route('home');
    }

    public static function registerWithPhone(Request $request)
    {
        $baseRules = [
            'g-recaptcha-response' => ['required', new RecaptchaRule],
            'role_phone' => ['required', 'string'],
            'phone' => ['required', 'string'],
        ];

        $organizationRules = [
            'g-recaptcha-response' => ['required', new RecaptchaRule],
            'organization_form_phone' => ['required', 'string'],
            'inn_phone' => ['required', 'string'],
        ];

        $data = $request->validate(
            $request->role_phone == 'user'
                ? $baseRules
                : array_merge($baseRules, $organizationRules)
        );

        if (User::where('phone', normalizePhone($data['phone']))->exists()) {
            return redirect()->back()->with('error', 'Пользователь с таким номером телефона уже существует, войдите в аккаунт.');
        }

        if ($data['role_phone'] == 'user') {
            $code = generateSixDigitCode();
            $token = generateRandomString(10);

            OtpCodes::create([
                'code' => $code,
                'phone' => $data['phone'],
                'token' => $token,
                'role' => $data['role_phone'],
            ]);

            if(env('API_WORK') == 'true'){
                $send_sms = sendSms($data['phone'], $code);
                if($send_sms != true){
                    return redirect()->back()->with('error', 'Ошибка отправки сообщения');
                }
            }
            return redirect()->route('register.verify.code')->with('token', $token);
        } else {
            $inn = $data['inn_phone'];
            $inn_user = User::where('inn', $data['inn_phone'])->get();
            if($inn_user->count() > 0){
                return redirect()->back()->with('error', 'Пользователь с таким ИНН уже существует.');
            }
            
            $status = 'Действующий';
            $contragent = '3edqwe';
            $okved = '';
            
            if(env('API_WORK') == 'true'){
                $organization_data = checkOrganizationInn($data['inn_phone']);
                $contragent = '3edqwe';
                $okved = $organization_data['okved'] ?? '';
            }
            
            return redirect()->route('confirm.inn.information.phone')->with([
                'phone' => $data['phone'],
                'role' => $data['role_phone'],
                'inn' => $inn,
                'organization_form' => $data['organization_form_phone'],
                'contragent' => $contragent,
                'status' => $status,
                'okved' => $okved,
            ]);
        }
    }

    public static function confirmInnInformationPhone()
    {
        $phone = session('phone');
        $inn = session('inn');
        $contragent = session('contragent');
        $status = session('status');
        $okved = session('okved');
        $role = session('role');
        $organization_form = session('organization_form');
        
        $existingOrganizations = Organization::where('inn', $inn)
            ->get(['id', 'title', 'email', 'phone', 'created_at','slug']);
        
        return view('auth.confirm-inn-information-phone', compact(
            'organization_form',
            'role',
            'phone',
            'inn',
            'contragent',
            'status',
            'okved',
            'existingOrganizations'
        ));
    }

    public static function acceptInnInformation(Request $request)
    {
        $data = $request->validate([
            'phone' => ['required', 'string'],
            'inn' => ['required', 'string'],
            'contragent' => ['required', 'string'],
            'status' => ['required', 'string'],
            'okved' => ['required', 'string'],
            'organization_form' => ['required', 'string'],
            'role' => ['required', 'string'],
            'organizations' => ['nullable', 'array'],
            'organizations.*' => ['nullable', 'integer'],
        ]);

        $code = generateSixDigitCode();
        $token = generateRandomString(10);

        OtpCodes::create([
            'code' => $code,
            'phone' => $data['phone'],
            'token' => $token,
            'inn' => $data['inn'],
            'okved' => $data['okved'],
            'contragent' => $data['contragent'],
            'role' => $data['role'],
            'organization_form' => $data['organization_form'],
            'organization_ids' => json_encode($data['organizations']) ?? null,
        ]);

        if(env('API_WORK') == 'true'){
            $send_sms = sendSms($data['phone'], $code);
            if($send_sms != true){
                return redirect()->back()->with('error', 'Ошибка отправки сообщения');
            }
        }

        return redirect()->route('register.verify.code')->with('token', $token);
    }

    public static function verifyCode()
    {
        $token = session('token');
        return view('auth.verify-code', compact('token'));
    }

    public static function verifyCodeSend(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string'],
            'token' => ['required', 'string'],
        ]);
        
        $codeModel = OtpCodes::orderBy('id', 'desc')->where('token', $data['token'])->first();
        
        if($codeModel != null && $codeModel->code != $data['code']){
            return redirect()->back()->with('error', 'Код введен неверно')->with('token', $data['token']);
        }
        
        $user = User::create([
            'phone' => $codeModel->phone,
            'role' => $codeModel->role,
            'inn' => $codeModel->inn,
            'organization_form' => $codeModel->organization_form,
        ]);
        
        Wallet::create(['user_id' => $user->id]);
        
        if (!empty($codeModel->organization_ids)) {
            Organization::whereIn('id', json_decode($codeModel->organization_ids))
                ->update(['user_id' => $user->id]);
        }
        
        Auth::login($user);
        return redirect()->route('home');
    }
}