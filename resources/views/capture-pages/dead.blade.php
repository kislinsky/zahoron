@include('header.header')



<section class="capture_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <h1 data-bs-toggle="modal" data-bs-target="#cemetery_choose_form" class="title">{{ $title_h1 }}</h1>
            <div class="video_service">
                <img class='btn_play_video' src="{{asset('storage/uploads/Group 34.svg')}}" alt="">
                <video controls src="{{asset('storage/'.get_acf(27,"video")) }}"></video>
            </div>
            <form action="{{ route('dead.send') }}" method="get" id='capture_form' class='form_popup capture_form'>
                    @csrf
                    <input type="hidden" name="time_now" class='input_time_now'>

                    <div class="flex_input_form_contacts flex_beautification_form">
                        <div class="block_input" >
                            <label for="">Выберите город</label>
                            <div class="block_ajax_input_search_cities">
                                <input class='input_search_cities' type="text" name="city_search" id="" value='{{ selectCity()->title }}'>
                                <input type="hidden" name="city_dead" class='city_id_input' value='{{ selectCity()->id }}'>
                            </div>
                            @error('city_dead')
                                <div class='error-text'>{{ $message }}</div>
                            @enderror
                        </div>  
                        <div class="block_input" >
                            <div class="flex_input"><label for="">Выберите морг</label> <label class='flex_input_checkbox checkbox'><input type="checkbox" name='none_mortuary'>Неизвестно</label></div>
                            <div class="select"><select name="mortuary_dead" >
                                {{ view('components.components_form.mortuaries',compact('mortuaries')) }}
                            </select></div>
                            @error('mortuary_dead')
                                <div class='error-text'>{{ $message }}</div>
                            @enderror
                        </div>  
                    </div>
                   
                    <div class="block_input" >
                        <label for="">Ф.И.О. Умершего</label>
                        <input  type="text" name="fio_dead" placeholder="Иванов Иван Иванович">
                        @error('fio_dead')
                            <div class='error-text'>{{ $message }}</div>
                        @enderror
                    </div>  
                    <div class="block_info_user_form">
                        <div class="flex_input_form_contacts flex_beautification_form">
                            <div class="block_input">
                                <label for="">Имя</label>
                                <input type="text" name='name_dead' placeholder="Имя" <?php if($user!=null){echo 'value='.$user->name;}?>>
                                @error('name_dead')
                                    <div class='error-text'>{{ $message }}</div>
                                @enderror
                            </div> 
                            <div class="block_input">
                                <label for="">Номер телефона</label>
                                <input type="text" class='phone' name="phone_dead" id="" placeholder="Номер телефона" <?php if(isset($user)){if($user!=null){echo 'value="'.$user->phone.'"';}}?> >
                                @error('phone_dead')
                                    <div class='error-text'>{{ $message }}</div>
                                @enderror
                            </div> 
                        </div>
                    </div>
                    <label class="aplication checkbox active_checkbox">
                        <input required type="checkbox" name="aplication"  checked >
                        <p>Я согласен на обработку персональных данных в соответствии с Политикой конфиденциальности</p>
                    </label>
                    <div class="g-recaptcha" data-sitekey="{{ config('recaptcha.site_key') }}"></div>
                        @error('g-recaptcha-response')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    <div class="flex_btn block_call_time">
                        <div class="btn_bac_gray open_call_time">
                            Позвонить по времени<img src='{{asset('storage/uploads/Vector 9 (1).svg')}}'>
                        </div>
                        <div class="call_time">
                            <input class="btn_bac_gray" type="time" name="call_time" id="">
                            <label class="aplication checkbox">
                                <input  type="checkbox" name="call_tomorrow" >
                                <p>Позвонить завтра</p>
                            </label>
                        </div>
                            
                        <button type='submit'class="blue_btn">Запросить информацию</button>
                    </div>
                </form>
        </div>
    </div>
     <img class='img_light_theme lily_left'src="{{asset('storage/uploads/lily-of-the-valley 1 2.svg')}}" alt="">
        <img class='img_light_theme lily_right'src="{{asset('storage/uploads/lily-of-the-valley 1 1.svg')}}" alt="">
        <img class='img_black_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1_black.svg')}}" alt="">
</section>

<section class="capture_block">
    <div class="container capture_container">


         {{view('capture-pages.components.advantages',compact('advantages_1_title',
            'advantages_1_text',
            'advantages_2_title',
            'advantages_2_text',
            'advantages_3_title',
            'advantages_3_text'))
        }}


        <div class="blue_btn" style="max-width: 600px;width:100%;">Сделать заявку!</div>

        <div class="gos_block gos_block_1">
            <img src="{{asset('storage/uploads/image 29.png')}}" alt="">  
            <div class="content_gos_block">
                <h2 class="title_blue_big">Государственные выплаты <span class='title_green_big'>+ 13500 рублей</span></h2>    
                <div class="text_gray">Выплаты производятся умершиим не работающим пенсионерам</div>
            </div>      
        </div>

        <a href='#capture_form' class="blue_btn" style="max-width: 600px;width:100%;">Сделать заявку!</a>

        {{view('capture-pages.components.our-works',compact('our_works'))}}
        
        {{view('capture-pages.components.how-work',compact('instruction_1_title',
            'instruction_1_text',
            'instruction_2_title',
            'instruction_2_text',
            'instruction_3_title',
            'instruction_3_text'))
        }}
        
        {{view('capture-pages.components.reviews',compact('reviews'))}}

         <div>
            <h2 class="title">{{ $text_block_title }}</h2>
            <div class="text_black">{{ $text_block_text }}</div>    
        </div>
    </div>
</section>
@include('footer.footer') 
