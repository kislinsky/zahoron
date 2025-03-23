<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCodes;
use App\Models\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;


    public static function resetPasswordWithPhone(Request $request){
        $data=request()->validate([
            'phone'=>['required']
        ]);

        $code = generateSixDigitCode();
        $token = generateRandomString(10);
        
        OtpCodes::create([
            'code' => $code,
            'phone' => $data['phone'],
            'token' => $token,
            'role'=>'user'
        ]);

        if(env('API_WORK')=='true'){
            $send_sms=sendSms($data['phone'],$code);
            if($send_sms!=true){
                return redirect()->back()->with('error','Ошибка отправки сообщения');
            }
        }

        return redirect()->route('reset-password.phone.verify.code')->with('token', $token);

    }


    public static function pageVerificateCode(){
        $token=session('token');
        if($token!=null){
            return view('auth.passwords.verificate-phone-code',compact('token'));
        }
        return redirect()->back()->with('error','Отправьте код занаво');
    }

    public static function verificateCode(Request $request){
        $data=request()->validate([
            'token'=>['required'],
            'code'=>['required','integer']
        ]);

        $code=OtpCodes::where('token',$data['token'])->orderByDesc('id')->first();
        if($code!=null && $code->code==$data['code']){
            $token=$data['token'];
            return redirect()->route('reset-password.phone.new')->with('token',$token);
        }
        return redirect()->route('password.request')->with('error','Отправьте код занаво');
    }

    public static function passwordNewPhone(){
        $token=session('token');
        if($token!=null){
            return view('auth.passwords.new-password-phone',compact('token'));
        }
        return redirect()->route('password.request')->with('error','Отправьте код занаво');
    }

    public static function acceptPasswordNewPhone(Request $request){
        $data=request()->validate([
            'password'=>['required'],
            'token'=>['required']
        ]);
        $otp_code=OtpCodes::where('token',$data['token'])->first();
        if($otp_code!=null){
            $user=User::where('phone',$otp_code->phone)->first();
            $user->update([
                'password'=>Hash::make($data['password'])
            ]);
            return redirect()->route('login')->with('message_cart','Ваш пароль успешно обновлен, войдите в свой личный кабинет.');
        }

        return redirect()->route('password.request')->with('error','Отправьте код занаво');
    }

}
