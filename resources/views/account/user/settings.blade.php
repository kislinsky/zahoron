@include('header.header-account')
<?php 
    use App\Models\Product;
    use App\Models\Service;
?>
<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <div class="index_title">Настройки</div>    
        </div>
        <img class='rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        
    </div>
</section>

<section class="settings">
    <div class="container">
        <form action='{{ route('account.user.settings.update') }}' method="get" class="form_settings">
            @csrf
            <div class="title_middle settings_margin_form" style='margin-top:0px;'>Профиль пользователя</div>

            <div class="flex_search_form">
                <div class="block_inpit_form_search">
                    <label class='label_input'>Имя</label>
                    <input type="text" name='name' value='{{ $user->name }}'placeholder='Имя'>
                    @error('name')
                        <div class='error-text'>{{ $message }}</div>
                    @enderror
                </div>
                <div class="block_inpit_form_search">
                    <label class='label_input'>Фамилия</label>
                    <input type="text" name='surname' value='{{ $user->surname }}'placeholder='Фамилия'>
                    @error('surname')
                        <div class='error-text'>{{ $message }}</div>
                    @enderror
                </div>
                <div class="block_inpit_form_search">
                    <label class='label_input'>Отчество</label>
                    <input type="text" name='patronymic' value='{{ $user->patronymic }}'placeholder='Отчество'>
                    @error('patronymic')
                        <div class='error-text'>{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="flex_search_form">
                <div class="block_inpit_form_search">
                    <label class='label_input'>Телефон</label>
                    <input type="phone" name='phone' value='{{ $user->phone }}'placeholder='Телефон'>
                    @error('phone')
                        <div class='error-text'>{{ $message }}</div>
                    @enderror
                </div>
                <div class="block_inpit_form_search">
                    <label class='label_input'>Город</label>
                    <input type="text" name='city' value='{{ $user->city }}'placeholder='Город'>
                    @error('city')
                        <div class='error-text'>{{ $message }}</div>
                    @enderror
                </div>
                <div class="block_inpit_form_search">
                    <label class='label_input'>Адрес</label>
                    <input type="text" name='adres' value='{{ $user->adres }}'placeholder='Улица, дом'>
                    @error('adres')
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

            <div class="block_inpit_form_search">
                 <div class="title_middle settings_margin_form">Настройки уведомлений</div>
                <label class='flex_input_checkbox'>
                    Email уведомления 
                    <label class="switch">
                        <input type="checkbox" name='email_notifications'  value='1' <?php if($user['email_notifications']!=null){ echo'checked';}?>>
                        <span class="slider"></span>
                    </label>
                </label>
                <label class='flex_input_checkbox '>
                    SMS уведомления
                    <label class="switch">
                        <input type="checkbox" name='sms_notifications' value='1' <?php if($user['sms_notifications']!=null){ echo'checked';}?>>
                        <span class="slider"></span>
                    </label>
            </div>

            <div class="block_inpit_form_search">
                
                <div class="title_middle settings_margin_form">Прочие настройки</div>
                <div class="flex_form_settings">
                    <div class="block_inpit_form_search">
                        <label class='label_input'>Язык интерфейса</label>
                        <select name='language'>
                            <option <?php if($user['language']==1){ echo'selected';}?> value="1">Русский</option>
                        </select>
                        @error('language')
                            <div class='error-text'>{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="block_inpit_form_search">
                        <label class='label_input'>Тема</label>
                        <select name='theme'>
                            <option  <?php if($user['theme']=='light'){ echo'selected';}?>value="light">Светлая</option>
                            <option <?php if($user['theme']=='dark'){ echo'selected';}?> value="dark">Темная</option>
                        </select>
                        @error('theme')
                            <div class='error-text'>{{ $message }}</div>
                        @enderror
                    </div>
                </div>
           </div>
           <button type="submit" class='blue_btn settings_margin_form'>Сохранить настройки</button>

        </form>
    </div>
</section>

@include('footer.footer') 