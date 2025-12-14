@include('header.header')

<div class="container">

    <div class="flex_titles_account flex_btn_center">
        <div class="btn_bac_gray active_label_product open_form_with_phone">По номеру телефона</div>
        <div class="btn_bac_gray open_form_with_email">По почте</div>
    </div>

    <form class='form_login form_with_email' method="POST" action="{{ route('register') }}" style="display: none;">
        @csrf
        <div class="title_li">Регистрация</div>
        <div class="text_li">Есть аккаунт? <a href="{{ route('login') }}">Войти</a></div>
        <div class="block_input">
            <select name="role" id="" class='select_role'>
                <option value="user">Пользователь</option>
                <option value="organization">Организация</option>
                <option value="organization-provider">Организация-поставщик</option>
            </select>
            @error('role')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div> 

        <div class="input_organization">
            <label for="inn">ИНН</label>
            <input id="inn" type="text" name="inn" value="{{ old('inn') }}" autofocus placeholder="ИНН">
        </div>

        <div class="input_organization">
            <label for="inn">Организационная форма</label>
            <select name="organization_form" id="">
                <option value="ep">Индивидуальный предприниматель</option>
                <option value="organization">Организация</option>
            </select>
        </div>

        <div class="block_input">
            <label for="email">Почта</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Почта">
            @error('email')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div> 
        
        <div class="block_input">
            <label for="password">Пароль</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Пароль">
            @error('password')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div> 

        <div class="block_input">
            <label for="password">Повторите пароль</label>
            <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="Повторите пароль">
        </div> 
        
        <div class="g-recaptcha" data-sitekey="{{ config('recaptcha.site_key') }}"></div>
        @error('g-recaptcha-response')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror
        
        <button type="submit" class="blue_btn">Зарегистрироваться</button>
    </form>

    <form class='form_login form_with_phone' method="POST" action="{{ route('register.phone') }}">
        @csrf
        <div class="title_li">Регистрация</div>
        <div class="text_li">Есть аккаунт? <a href="{{ route('login') }}">Войти</a></div>
        <div class="block_input">
            <select name="role_phone" id="" class='select_role'>
                <option value="user">Пользователь</option>
                <option value="organization">Организация</option>
                <option value="organization-provider">Организация-поставщик</option>
            </select>
            @error('role')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div> 
        
        <div class="input_organization">
            <label for="inn_phone">ИНН</label>
            <input id="inn_phone" type="text" name="inn_phone" value="{{ old('inn_phone') }}" autofocus placeholder="ИНН">
        </div>

        <div class="input_organization">
            <label for="inn">Организационная форма</label>
            <select name="organization_form_phone" id="">
                <option value="ep">Индивидуальный предприниматель</option>
                <option value="organization">Организация</option>
            </select>
        </div>

        <div class="block_input">
            <label for="email">Телефон</label>
            <input class='phone' id="phone" type="text" name="phone" value="{{ old('phone') }}" required autocomplete="phone" autofocus placeholder="Телефон">
            @error('phone')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div> 
        
        <div class="g-recaptcha" data-sitekey="{{ config('recaptcha.site_key') }}"></div>
        @error('g-recaptcha-response')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror
        
        <button type="submit" class="blue_btn">Зарегистрироваться</button>
    </form>
</div>

@include('footer.footer')