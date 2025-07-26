<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\RecaptchaRule;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
        $this->redirectTo = '/' . selectCity()->slug . '/home';

    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }


    public static function loginWithPhone(Request $request){

        $data=request()->validate([
            'g-recaptcha-response' => ['required', new RecaptchaRule],
            'phone'=>['required','string'],
            'password_phone'=>['required','string'],
        ]);

        $users=User::where('phone',normalizePhone($data['phone']))->get();
        if(isset($users[0])){
            if (Hash::check($data['password_phone'], $users->first()->password)){
                Auth::login($users->first());
                return redirect()->route('home');
            }
        }
        return redirect()->back()->with('error','Неверный пароль или логин');
    }

}
