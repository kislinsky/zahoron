@include('header.header')

<div class="container">
    <form class='form_login'method="POST" action="{{ route('register') }}">
        @csrf
        <div class="title_li">Регистрация</div>
        <div class="text_li">Есть аккаунт? <a  href="/login">  Войти</a></div>
        <div class="block_input">
            <select name="role" id="">
                <option value="user">Пользователь</option>
                <option value="organization">Организация</option>
                <option value="agent">Агент</option>
                <option value="worker">Работник</option>
            </select>
            @error('role')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div> 
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


            <div class="block_input">
                <label for="password">Повторите пароль</label>
                <input id="password-confirm" type="password"  name="password_confirmation" required autocomplete="Повторите пароль">

                
            </div> 

            <button type="submit" class="blue_btn">Зарегистрироваться</button>
        

           
    </form>
</div>

@include('footer.footer') 







