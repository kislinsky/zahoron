@extends('account.agent.components.page')
@section('title', 'Настройки')
@include('forms.location-2')

@section('content')

<div class="bac_black input_print_form">
    <div class='message'>
        <div class="flex_title_message">
            <div class="title_middle">Добавить фото</div>
            <div class="close_message">
                <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
            </div>
        </div>
        <form action='{{ route('account.agent.upload-seal.add') }}' method='post' enctype='multipart/form-data' class="form_settings">
            @csrf
            <div class="block_inpit_form_search input_print">
                <div class="input__wrapper">
                    <input style='display:none;' name="file_print[]" type="file" id="input__file" multiple class="input input__file_2">
                    <label for="input__file" class="input__file-button">
                    <span class="input__file-button-text_2"><img src='{{ asset('/storage/uploads/add-icon.svg') }}'>Допускается загрузка фотографии в формате JPG и PNG размером не более 8 МБ.<br>Перетаскивайте фотографии прямо в эту область</span>
                    </label>
                </div>
                @error('file_print')
                    <div class='error-text'>{{ $message }}</div>
                @enderror
            </div>
            <button class="blue_btn btn_100">Загрузить</button>
        </form>
    </div>
</div>






<section class="settings">
    <div class="container">
        <form action='{{ route('account.agent.settings.update') }}' method='post' enctype='multipart/form-data' class="form_settings">
            @csrf
            <div class="title_middle settings_margin_form" style='margin-top:0px;'>Профиль пользователя</div>
           
            <div class="title_news settings_margin_form">Данные для заключения договора</div>
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
                <div class="title_middle settings_margin_form">Загрузка печати/подписи</div>
                <div class="flex_input_print">
                    <div class="gray_btn open_form_print">Выберите файл <img src='{{ asset('/storage/uploads/Add.svg') }}'></div>
                    @if (isset($imgs_agent))
                        @if ($imgs_agent!=null)
                        <div class="ul_img_agent">
                            @foreach ($imgs_agent as $img_agent)
                                <div class="img_agent">
                                    <a href='{{ route('account.agent.upload-seal.delete',$img_agent->id) }}'class="bac_img_agent">
                                        <img src="{{ asset('storage/uploads/Group 36.svg') }}" alt="">
                                    </a>
                                    <img src="{{ asset('storage/uploads_agent/'.$img_agent->title) }}" alt="">
                                </div>
                            @endforeach
                        </div>
                        @endif
                    @endif
                </div>
            </div>
            
                

            <div class="title_middle settings_margin_form">Данные оплаты</div>
            <div class="flex_form_settings">
                <div class="block_inpit_form_search">
                    <label class='label_input'>Номер карты</label>
                    <input type="password" autocomplete="off" name='number_cart' value='{{ $user->number_cart }}'placeholder='Номер карты'>

                    @error('number_cart')
                        <div class='error-text'>{{ $message }}</div>
                    @enderror
                </div>
                <div class="block_inpit_form_search">
                    <label class='label_input'>Банк</label>
                    <select name='bank'>
                        <option  value="Сбербанк">Сбербанк</option>
                    </select>
                    @error('bank')
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
           


           {{view('account.agency.components.settings-organization.cemeteries',compact('cemeteries'))}}


           
           <button type="submit" class='blue_btn settings_margin_form'>Сохранить настройки</button>

            
        </form>
    </div>
</section>

@endsection
