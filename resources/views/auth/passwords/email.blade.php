

<?php

use Illuminate\Support\Facades\Route;

?>
@include('header.header')

<div class="container">

    <div class="flex_titles_account flex_btn_center">
        <div class="btn_bac_gray active_label_product open_form_with_email">По почте</div>
        <div class="btn_bac_gray open_form_with_phone">По номеру телефона</div>
    </div>


    <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

      
    </div>

    <form class='form_login form_with_email password_reset' method="POST" action="{{ route('password.email') }}">
        @csrf
         <div class="block_input">
            <label for="email">Почта</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

            @error('email')
                <span class="error-text">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <button type="submit" class="blue_btn">
            {{ __('Send Password Reset Link') }}
        </button>
           
    </form>

    <form class='form_login form_with_phone password_reset' method="POST" action="{{ route('reset-password.phone') }}">
        @csrf
         <div class="block_input">
            <label for="email">Телефон</label>
            <input  class='phone' id="email" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required autocomplete="phone" autofocus>

            @error('phone')
                <span class="error-text">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <button type="submit" class="blue_btn">
           Отправить код на номер телефона
        </button>
           
    </form>
</div>

@include('footer.footer') 