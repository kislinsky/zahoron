@include('header.header')

<div class="container">
    <form class='form_login' method="POST" action="{{ route('register.verify.code.send') }}">
        @csrf
        
        <input type="hidden" name="token" value='{{ session('token') }}'>

        <div class="title_li">Подтверждение регистрации</div>
        <div class="block_input">
            <label for="phone">Введите код, присланный по SMS</label>
            <input id="email" type="integer"  name="code" value="{{ old('code') }}" required autocomplete="code" autofocus placeholder="3423424242">
            @error('code')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div> 
        <div class="text_gray">Выслать повторно (2 минуты)</div>
        <button type="submit" class="blue_btn">Подтвердить</button>
    </form>
</div>

@include('footer.footer') 