<?php

namespace App\Http\Controllers\Auth;

use App\Models\City;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->redirectTo = '/' . $this->selectCity()->slug . '/home';
    }

    /**
     * Select the city.
     *
     * @return object
     */
    protected function selectCity()
    {
        if(isset($_COOKIE['city'])){
            return $city=City::findOrFail($_COOKIE['city']);
        }
        $city=City::where('selected_admin',1)->first();
        setcookie("city", $city->id, time()+20*24*60*60,'/');
        return $city;
    }
}