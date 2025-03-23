@include('header.header')

<div class="container">
    <form class='form_login  password_reset'method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">
        <div class="block_input">
            <label for="">Почта</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
            @error('email')
                <span class="error-text" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
      

        <div class="block_input">
            <label for="">Пароль</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
            @error('password')
                <span class="error-text" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div> 
            
        <div class="block_input">
            <label for="">Подвердите  пароль</label>
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
            
        </div> 

        <button type="submit" class="blue_btn">
            {{ __('Reset Password') }}
        </button>
            
    </form>
</div>
@include('footer.footer') 