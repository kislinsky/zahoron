<div class="block_inpit_form_search">
    <div class="title_middle settings_margin_form">Контактная информация</div>
    <div class="flex_search_form">
        <div class="block_inpit_form_search">
            <label class='label_input'>Телефон</label>
            <input type="phone" name='phone' value='{{ $user->phone }}'placeholder='Телефон'>
            @error('phone')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>
        <div class="block_inpit_form_search">
            <label class='label_input'>Telegram </label>
            <input type="text" name='telegram' value='{{ $user->telegram }}'placeholder='+758435348053'>
            @error('telegram')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="flex_search_form">
        <div class="block_inpit_form_search">
            <label class='label_input'>Email</label>
            <input type="email" name='email' value='{{ $user->email }}'placeholder='zahoron@gmail.com'>
            @error('email')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>
        <div class="block_inpit_form_search">
            <label class='label_input'>WhatsApp </label>
            <input type="text" name='whatsapp' value='{{ $user->whatsapp }}'placeholder='+758435348053'>
            @error('watsapp')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="flex_search_form">
        <div class="block_inpit_form_search">
            <label class='label_input'>Пароль</label>
            <input type="password" autocomplete="off" name='password' placeholder='Старый пароль'>
            @error('password')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>
        <div class="block_inpit_form_search">
            <input type="password" name='password_new' placeholder='Новый пароль'>
            @error('password_new')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>
        <div class="block_inpit_form_search">
            <input type="password" name='password_new_2' placeholder='Новый пароль (ещё раз)'>
            @error('password_new_2')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>