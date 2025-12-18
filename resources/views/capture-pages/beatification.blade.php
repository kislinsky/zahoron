@include('header.header')



<section class="capture_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <h1 data-bs-toggle="modal" data-bs-target="#cemetery_choose_form" class="title">{{ $title_h1 }}</h1>
            <div class="text_page_marketplace">от {{ selectCity()->organizations->count() ?? 0 }} ритуальных агенств</div>
            <form action="{{ route('beautification.send') }}" method="get" id='beautification_form_capture' class='form_popup capture_form beautification_form_capture'>
                    @csrf
                    @if(isset($product))
                        <input type="hidden" name="burial_id_beautification" value={{$product->id}}>
                    @endif
                    <input type="hidden" name="time_now" class='input_time_now'>

                    <div class="flex_input_form_contacts flex_beautification_form">
                        <div class="block_input" >
                            <label for="">Выберите город</label>
                            <div class="block_ajax_input_search_cities">
                                <input class='input_search_cities' type="text" name="city_search" id="" value='{{ selectCity()->title }}'>
                                <input type="hidden" name="city_beautification" class='city_id_input' value='{{ selectCity()->id }}'>
                            </div>
                            @error('city_beautification')
                                <div class='error-text'>{{ $message }}</div>
                            @enderror
                        </div>  
                        <div class="block_input" >
                            <label for="">Выберите кладбище</label>
                            <div class="select"><select name="cemetery_beautification" >
                                {{view('components.components_form.cemetery',compact('cemeteries_beatification'));}}
                            </select></div>
                            @error('cemetery_beautification')
                                <div class='error-text'>{{ $message }}</div>
                            @enderror
                        </div>  
                    </div>


                    <div class="block_input beautification_form_checkbox">
                        <div class="title_news">Выберите товар</div>
                        @if(count($categories_product_price_list)>0)
                            <div  class="ul_products_radio">
                                @foreach($categories_product_price_list as $category_product_price_list)
                                    <label class="checkbox">
                                        <input type="checkbox" value={{$category_product_price_list->id}} name='products_beautification[]'>{{$category_product_price_list->title}}
                                    </label>
                                @endforeach
                            </div>
                        @endif
                        @error('product_beautification')
                            <div class='error-text'>{{ $message }}</div>
                        @enderror
                    </div>  

                   
                    <div class="block_info_user_form border_top_beautification_form">
                        <div class="flex_input_form_contacts flex_beautification_form">
                            <div class="block_input">
                                <label for="">Имя</label>
                                <input type="text" name='name_beautification' placeholder="Имя" <?php if($user!=null){echo 'value='.$user->name;}?>>
                                @error('name_beautification')
                                    <div class='error-text'>{{ $message }}</div>
                                @enderror
                            </div> 
                            <div class="block_input">
                                <label for="">Номер телефона</label>
                                <input type="text"   class='phone' name="phone_beautification" id="" placeholder="Номер телефона" <?php if(isset($user)){if($user!=null){echo 'value="'.$user->phone.'"';}}?> >
                                @error('phone_beautification')
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
            <video controls src="{{asset('storage/'.get_acf(26,"video")) }}"></video>
        </div>

        {{view('capture-pages.components.our-works',compact('our_works'))}}

        {{view('capture-pages.components.how-work')}}

        <a href='#beautification_form_capture' class="blue_btn" style="max-width: 600px;width:100%;">Сделать заявку!</a>

        {{view('capture-pages.components.reviews',compact('reviews'))}}

    </div>
</section>
@include('footer.footer') 
