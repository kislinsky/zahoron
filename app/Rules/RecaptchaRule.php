<?php

namespace App\Rules;

use ReCaptcha\ReCaptcha;
use Illuminate\Contracts\Validation\Rule;

class RecaptchaRule implements Rule
{
    public function passes($attribute, $value)
    {
        $recaptcha = new ReCaptcha(env('RECAPTCHA_SECRET_KEY'));
        $response = $recaptcha->verify($value, request()->ip());

        return $response->isSuccess();
    }

    public function message()
    {
        return 'Пожалуйста, подтвердите, что вы не робот.';
    }
}