@include('header.header')



<section class="capture_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <h1 data-bs-toggle="modal" data-bs-target="#cemetery_choose_form" class="title">{{ $title_h1 }}</h1>
            <div class="text_page_marketplace">от {{ selectCity()->organizations->count() ?? 0 }} ритуальных агенств</div>
            <form action="{{ route('funeral-service.send') }}" id='capture_form' шmethod="get" class='form_popup capture_form'>
                    @csrf
                    <input type="hidden" name="time_now" class='input_time_now'>

                    <div class="flex_input_form_contacts flex_beautification_form">
                        <div class="block_input" >
                            <label class='label_city'for="">Город отправки</label>
                            <div class="block_ajax_input_search_cities">
                                <input class='input_search_cities' type="text" name="city_search" id="" value='{{ selectCity()->title }}'>
                                <input type="hidden" name="city_funeral_service" class='city_id_input' value='{{ selectCity()->id }}'>
                            </div>
                            @error('city_funeral_service')
                                <div class='error-text'>{{ $message }}</div>
                            @enderror
                        </div>  

                    </div>
                    <div class="flex_input_form_contacts flex_beautification_form">

                       
                        <div class="block_input service_cargo_200" >
                            <div class="flex_input"><label for="">Город получения</label> <label class='flex_input_checkbox checkbox'><input type="checkbox" name='none_mortuary'>Заграница</label></div>
                            <div class="block_ajax_input_search_cities">
                                <input search='false' class='input_search_cities' type="text" name="city_search" id="" value='{{ selectCity()->title }}'>
                                <input  type="hidden" name="city_funeral_service_to" class='city_funeral_service_to city_id_input' value='{{ selectCity()->id }}'>
                            </div>
                            @error('city_funeral_service_to')
                                <div class='error-text'>{{ $message }}</div>
                            @enderror
                        </div>  

                        <div class="block_input" >
                            <div class="flex_input"><label for="">Выберите морг</label> <label class='flex_input_checkbox checkbox' ><input type="checkbox" name='none_mortuary'>Неизвестно</label></div>
                            <div class="select"><select name="mortuary_funeral_service" >
                                {{ view('components.components_form.mortuaries',compact('mortuaries')) }}
                            </select></div>
                            @error('mortuary_funeral_service')
                                <div class='error-text'>{{ $message }}</div>
                            @enderror
                        </div>  

                    </div> 

                    <div class="flex_input_form_contacts flex_beautification_form">

                        <div class="block_input" >
                           <label for="">Статус умершего</label> 
                           <div class="select"> <select name="status_death_people_funeral_service" >
                               <option value="Неработающий пенсионер">Неработающий пенсионер</option>
                            </select></div>
                            @error('status_death_people_funeral_service')
                                <div class='error-text'>{{ $message }}</div>
                            @enderror
                        </div>  

                        <div class="block_input" >
                            <label for="">Гражданский статус</label> 
                             <div class="select"><select name="civilian_status_people_funeral_service" >
                                <option value="Гражданский">Гражданский</option>
                             </select></div>
                             @error('city_memorial')
                                 <div class='error-text'>{{ $message }}</div>
                             @enderror
                         </div>  

                    </div> 

                    <div class="flex_input_form_contacts flex_beautification_form">

                        <div class="block_input" >
                            <label class="aplication checkbox">
                                <input  type="checkbox" name="funeral_service_church"  >
                                <p>Отпевание в церкви</p>
                            </label>
                        </div>  

                        <div class="block_input" >
                            <label class="aplication checkbox">
                                <input  type="checkbox" name="farewell_hall"  >
                                <p>Прощальный зал</p>
                            </label>
                         </div>  

                    </div> 

                    <div class="block_info_user_form">
                        <div class="flex_input_form_contacts ">
                            <div class="block_input">
                                <label for="">Имя</label>
                                <input type="text" name='name_funeral_service' placeholder="Имя" <?php if($user!=null){echo 'value='.$user->name;}?>>
                                @error('name_memorial')
                                    <div class='error-text'>{{ $message }}</div>
                                @enderror
                            </div> 
                            <div class="block_input">
                                <label for="">Номер телефона</label>
                                <input type="text" class='phone' name="phone_funeral_service" id="" placeholder="Номер телефона" <?php if(isset($user)){if($user!=null){echo 'value="'.$user->phone.'"';}}?> >
                                @error('phone_memorial')
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
                        
                        <button type='submit'class="blue_btn">Получить ценовое предложение</button>
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

        {{view('capture-pages.components.advantages')}}
        
        <div class="video_service">
            <img class='btn_play_video' src="{{asset('storage/uploads/Group 34.svg')}}" alt="">
            <video controls src="{{asset('storage/'.get_acf(22,"video")) }}"></video>
        </div>

        {{view('capture-pages.components.our-works',compact('our_works'))}}

        {{view('capture-pages.components.how-work')}}

        <a href='#capture_form' class="blue_btn" style="max-width: 600px;width:100%;">Сделать заявку!</a>

        {{view('capture-pages.components.reviews',compact('reviews'))}}
    </div>
</section>
@include('footer.footer') 
