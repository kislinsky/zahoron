@include('header.header-agent')
@include('forms.location')

<?php 
    use App\Models\Product;
    use App\Models\Service;
?>




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
        <form action='{{ route('account.agent.settings.update') }}' method='post' enctype='multipart/form-data' class="form_settings">
            @csrf
            <div class="title_middle settings_margin_form" style='margin-top:0px;'>Профиль пользователя</div>
            <div class="flex_form_settings">
                <div class="block_inpit_form_search">
                    <label class='label_input'>ИНН</label>
                    <input class='inn_input'type="text" name='inn' value='{{ $user->inn }}'placeholder='ИНН'>
                    @error('inn')
                        <div class='error-text'>{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="title_news settings_margin_form">Данные агента</div>
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
           
           <div class="block_inpit_form_search">
                
                <div class="title_middle settings_margin_form">Список кладбищ:</div>

                <div class="block_input input_location_flex">
                    <div class="input_location_settings">
                        <div class="input_location">
                            <input type="hidden" name="id_cemetery" >
                            <img  data-bs-toggle="modal" data-bs-target="#location_form" class='open_location' src="{{ asset('storage/uploads/Закрыть.svg') }}" alt="">
                            <input type="text" name='location' placeholder='Расположение'>
                        </div>
                        <div class='text_location_input'>Впишите название кладбища (или района/
                            области) либо нажмите "+" и выберите из списка</div>
                    </div>
                    
                    <div class="blue_btn add_cemetery">Добавить кладбище</div>
                </div> 
                <div class="ul_cemtery">
                    @if (isset($cemeteries))
                        @if (count($cemeteries)>0)
                            @foreach ($cemeteries as $cemetery)
                            <div class="li_cemetery_agent">
                                <div class="mini_flex_li_product">
                                    <input type="hidden" value='{{ $cemetery->id }}'name="cemetery_ids[]">
                                    <div class="title_label">{{ $cemetery->title }}</div>
                                    <div class="text_li">Адрес: {{ $cemetery->adres }}</div>
                                </div>
                                <div  class="delete_cart delete_cemetery"><img src="{{asset('storage/uploads/Закрыть (1).svg')}}" alt=""></div>
                            </div>
                                
                            @endforeach
                        @endif
                    @endif
                </div>
        </div>
           
           <button type="submit" class='blue_btn settings_margin_form'>Сохранить настройки</button>

            
        </form>
    </div>
</section>

<script>
    $( ".add_cemetery" ).on( "click", function() {
    
        let id_location= $(this).siblings('.input_location_settings').children('.input_location').children('input[name="id_cemetery"]').val();
        let name_location = $(this).siblings('.input_location_settings').children('.input_location').children('input[name="location"]').val();
        $.ajax({
            type: 'POST',
            url: '{{ route("add.cemetery.settings") }}',
            data: {
                "_token": "{{ csrf_token() }}",
                'id_location': id_location,
                'name_location': name_location,
            }, success: function (result) {
                
                if(result['error']){
                    alert(result['error'])
                }else{
                    $('.ul_cemtery').append('<div class="li_cemetery_agent"><div class="mini_flex_li_product"><input type="hidden" value="'+result['id_cemetery']+'"name="cemetery_ids[]"><div class="title_label">'+name_location+'</div><div class="text_li">Адрес: "'+result['adres']+'"</div></div><div  class="delete_cart delete_cemetery"><img src="{{asset('storage/uploads/Закрыть (1).svg')}}" alt=""></div></div>' );
                }
            },
            error: function () {
                alert('Ошибка');
            }
        });
    
    });
    
</script>

@include('footer.footer') 
