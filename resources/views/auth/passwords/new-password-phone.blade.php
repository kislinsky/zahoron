@include('header.header')

<div class="container">

    <form class='form_login  password_reset' method="POST" action="{{ route('reset-password.phone.new.accept') }}">
        @csrf
         <div class="block_input">
            <label for="email">Пароль</label>
            <input   id="email" type="password" class="form-control @error('password') is-invalid @enderror" name="password" value="{{ old('password') }}" required autofocus>
            <input  id="email" type="hidden" name="token" value="{{ $token }}" required autofocus>

            @error('password')
                <span class="error-text">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <button type="submit" class="blue_btn">
           Обновить пароль
        </button>
           
    </form>
</div>

@include('footer.footer') 