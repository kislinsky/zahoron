<?php

use Illuminate\Support\Facades\Route;

?>
@include('header.header')

<div class="container">

    <div class="flex_titles_account flex_btn_center">
        <div class="btn_bac_gray active_label_product open_form_with_email">По почте</div>
        <div class="btn_bac_gray open_form_with_phone">По номеру телефона</div>
    </div>

    <form class='form_login form_with_email' method="POST" action="{{ route('login') }}">
        @csrf
        <div class="title_li">Вход</div>
        <div class="text_li">Нет аккаунта? <a  href="{{route('register')}}">  Регистрация</a></div>

            <div class="block_input">
                <label for="email">Почта</label>
                <input id="email" type="email"  name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Почта">
                @error('email')
                    <div class='error-text'>{{ $message }}</div>
                @enderror
            </div> 
            
            <div class="block_input">
                <label for="password">Пароль</label>
                <input id="password" type="password"  name="password" required autocomplete="current-password" placeholder="Пароль">
                @error('password')
                    <div class='error-text'>{{ $message }}</div>
                @enderror
            </div> 
            <div class="block_input block_form_check">
                <div class="form-check">
                    <input  type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">Запомнить меня</label>
                </div>
                @if (Route::has('password.request'))
                <div class="text_li">Забыли пароль? <a  href="{{ route('password.request') }}"> Восстановить</a></div>
            @endif

            </div> 
            <div class="g-recaptcha" data-sitekey="{{ config('recaptcha.site_key') }}"></div>
                @error('g-recaptcha-response')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            <button type="submit" class="blue_btn">Войти</button>
        

           
    </form>
    <form class='form_login form_with_phone'method="POST" action="{{ route('login.phone') }}">
        @csrf
        <div class="title_li">Вход</div>
        <div class="text_li">Нет аккаунта? <a  href="{{route('register')}}">  Регистрация</a></div>

            <div class="block_input">
                <label for="phone">Телефон</label>
                <input class='phone' id="phone" type="text"  name="phone" value="{{ old('phone') }}" required autocomplete="phone" autofocus placeholder="Телефон">
                @error('phone')
                    <div class='error-text'>{{ $message }}</div>
                @enderror
            </div> 
            
            <div class="block_input">
                <label for="password_2">Пароль</label>
                <input id="password_2" type="password"  name="password_phone" required autocomplete="current-password" placeholder="Пароль">
                @error('password_phone')
                    <div class='error-text'>{{ $message }}</div>
                @enderror
            </div> 
            <div class="block_input block_form_check">
                <div class="form-check">
                    <input  type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">Запомнить меня</label>
                </div>
                @if (Route::has('password.request'))
                <div class="text_li">Забыли пароль? <a  href="{{ route('password.request') }}"> Восстановить</a></div>
            @endif

            </div> 
            <div class="g-recaptcha" data-sitekey="{{ config('recaptcha.site_key') }}"></div>
                @error('g-recaptcha-response')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            <button type="submit" class="blue_btn">Войти</button>
        

           
    </form>
</div>

@include('footer.footer') 