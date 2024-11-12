@include('header.header')

<div class="container">
    <form class='form_login'method="POST" action="{{ route('login') }}">
        @csrf
        <div class="title_li">Вход</div>
        <div class="text_li">Нет аккаунта? <a  href="/register">  Регистрация</a></div>
        {{-- <div class="block_input">
            <select name="role" id="">
                <option value="user">Пользователь</option>
                <option value="organization">Организация</option>
                <option value="agent">Агент</option>
                <option value="worker">Работник</option>
            </select>
            @error('role')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>  --}}

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
            <button type="submit" class="blue_btn">Войти</button>
        

           
    </form>
</div>

@include('footer.footer') 