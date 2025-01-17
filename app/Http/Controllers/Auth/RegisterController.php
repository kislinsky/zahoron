<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCodes;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'role' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'role'=>$data['role'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public static function registerWithPhone(Request $request){
        $data=request()->validate([
            'role_phone'=>['required','string'],
            'phone'=>['required','string'],
        ]);

        $user=User::where('phone',$data['phone'])->get();
        if(isset($user[0])){
            redirect()->back()->with('error','Пользователь с таким номером телефона уже существует, войдите в аккаунт.');
        }
        $code=generateSixDigitCode();
        $token=generateRandomString(10);
        $codeModel=OtpCodes::create([
            'code'=>$code,
            'phone'=>$data['phone'],
            'token'=>$token,
            'role'=>$data['role_phone'],
        ]);
        // $send_sms=sendSms($data['phone'],$code);
        // if($send_sms!=true){
        //     redirect()->back()->with('error','Ошибка отправки сообщения');
        // }
        return redirect()->route('register.verify.code')->with('token', $token);
    }
    
    public static function verifyCode(){
        $token=session('token');
        return view('auth.verify-code',compact('token'));
    }

    public static function  verifyCodeSend(Request $request){
        $data=request()->validate([
            'code'=>['required','string'],
            'token'=>['required','string'],
        ]);
        $codeModel=OtpCodes::orderBy('id', 'desc')->where('token',$data['token'])->first();
        if($codeModel!=null && $codeModel->code!=$data['code']){
            return redirect()->back()->with('error','Код веден неверно')->with('token',$data['token']);
        }
        $user=createUserWithPhone($codeModel->phone,'user',$codeModel->role); 
        Auth::login($user);
        return redirect()->route('index');       
}
}
