@include('header.header')

<div class="container">

    <form class='form_login  password_reset' method="POST" action="{{ route('reset-password.phone.verify.code.send') }}">
        @csrf
         <div class="block_input">
            <label for="email">Код</label>
            <input   id="email" type="text" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ old('code') }}" required autofocus>
            <input  id="email" type="hidden" name="token" value="{{ $token }}" required autofocus>

            @error('code')
                <span class="error-text">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <button type="submit" class="blue_btn">
           Подвердить код
        </button>
           
    </form>
</div>

@include('footer.footer') 