
@include('header.header')
{{view('components.shema-org.burial',compact('product'))}}

@include('forms.life-story')

@include('forms.editing-burial')




<div id='image_personal' class="bac_black input_print_form">
    <div class='message'>
        <div class="flex_title_message">
            <div class="title_middle">Добавить фото</div>
            <div class="close_message">
                <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
            </div>
        </div>
        <form action='{{ route('burial.image-personal.add') }}' method='post' enctype='multipart/form-data' class="form_settings">
            @csrf
            <div class="block_inpit_form_search input_print">
                <input type="hidden" name="burial_id_image_personal" value='{{ $product->id }}'>
                <div class="input__wrapper">
                    <input style='display:none;' name="file_burials_image_personal[]" type="file" id="input__file" multiple class="input input__file_2">
                    <label for="input__file" class="input__file-button">
                    <span class="input__file-button-text_2"><img src='{{ asset('/storage/uploads/add-icon.svg') }}'>Допускается загрузка фотографии в формате JPG и PNG размером не более 8 МБ.<br>Перетаскивайте фотографии прямо в эту область</span>
                    </label>
                </div>
                @error('file_burials_image_personal')
                    <div class='error-text'>{{ $message }}</div>
                @enderror
            </div>
            <button class="blue_btn btn_100">Сохранить</button>
        </form>
    </div>
</div>


<div id='image_monument' class="bac_black input_print_form">
    <div class='message'>
        <div class="flex_title_message">
            <div class="title_middle">Добавить фото</div>
            <div class="close_message">
                <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
            </div>
        </div>
        <form action='{{ route('burial.image-monument.add') }}' method='post' enctype='multipart/form-data' class="form_settings">
            @csrf
            <div class="block_inpit_form_search input_print">
                <input type="hidden" name="burial_id_image_monument" value='{{ $product->id }}'>
                <div class="input__wrapper">
                    <input style='display:none;' name="file_burials_image_monument[]" type="file" id="input__file_2" multiple class="input input__file_2">
                    <label for="input__file_2" class="input__file-button_2">
                    <span class="input__file-button-text_2"><img src='{{ asset('/storage/uploads/add-icon.svg') }}'>Допускается загрузка фотографии в формате JPG и PNG размером не более 8 МБ.<br>Перетаскивайте фотографии прямо в эту область</span>
                    </label>
                </div>
                @error('file_burials_image_monument')
                    <div class='error-text'>{{ $message }}</div>
                @enderror
            </div>
            <button class="blue_btn btn_100">Сохранить</button>
        </form>
    </div>
</div>





<section class="order_page bac_gray">
    <div class="container">
       <div class="grid_two_single_product">
            <div class="block_single_product">
                <div class="swiper-button-next swiper_button_next_rewies"><img src='{{asset('storage/uploads/Переключатель.svg')}}'></div>
                <div class="swiper-button-prev swiper_button_prev_rewies"><img src='{{asset('storage/uploads/Переключатель (1).svg')}}'></div>
                <div class="swiper burial_swiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="img_single_product"> 
                                <img src="{{$product->urlImg() }}" alt="">
                                <div class="white_btn">{{ $product->who }}</div>
                            </div>
                        </div>
                        @if (isset($image_monument))
                            @if (count($image_monument)>0)
                                @foreach ($image_monument as $image_monument_one )
                                    <div class="swiper-slide">
                                        <div class="img_single_product"> 
                                            <img src="{{ $image_monument_one->urlImg()}}" alt="">
                                            <div class="white_btn">{{ $product->who }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endif
                        @if (isset($image_personal))
                            @if (count($image_personal)>0)
                                @foreach ($image_personal as $image_personal_one )
                                    <div class="swiper-slide">
                                        <div class="img_single_product"> 
                                            <img src="{{ $image_personal_one->urlImg()}}" alt="">
                                            <div class="white_btn">{{ $product->who }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="two_block_li_product">
                <div class="mini_flex_li_product">
                    <div class="index_title">{{ $product->surname }} {{ $product->name }} {{ $product->patronymic }}</div>
                    <div class="text_li">{{timeDifference( $product->date_birth,$product->date_death)->y}} лет</div>
                </div>
                <div class="mini_flex_li_product">
                    <div class="title_label">Даты захоронения:</div>
                    <div class="text_li">{{ $product->date_birth }}-{{ $product->date_death }}</div>
                </div>
                <div class="mini_flex_li_product">
                    <div class="title_label">Место захоронения:</div>
                    <div class="text_li">{{ $product->location_death }}</div>
                </div>
                <div class="mini_flex_li_product">
                    <div class="title_label">Поделиться:</div>

                    <div class="flex_icons_footer">
                        <a href="https://telegram.me/share/url?url={{ route('burial.single',$product->id) }}"><img src="{{asset('storage/uploads/socials (4).svg')}}" alt=""></a>
                        <a href="https://vk.com/share.php?url={{ route('burial.single',$product->id) }}"><img src="{{asset('storage/uploads/vk (3).svg')}}" alt=""></a>
                        <a href="whatsapp://send?text={{ route('burial.single',$product->id) }}" data-action="share/whatsapp/share"><img src="{{asset('storage/uploads/socials (3).svg')}}" alt=""></a>
                    </div>
                </div>
                <div class="flex_btn_li_product">
                    <a href='{{ route('burial.add',$product->id) }}'class="blue_btn">Получить координаты</a>
                    <a  href='{{ route('favorite.add',$product->id) }}' class="btn_border_blue img_mini_star"><img src="{{ asset('storage/uploads/Star 1 (1).svg')}}" alt=""></A>
                </div>
                
            </div>
       </div>
    </div>
</section>

<section class="price_service">
    <div class="container grid_two_page">
        <div class="block_main_single_product">
            <div class="flex_label_block">
                <div id_label='4'class="li_label_block active_label_product">Заказать услугу</div>
                <div id_label='1'class="li_label_block">История жизни</div>
                <div id_label='2'class="li_label_block">Информация</div>
                <div id_label='3'class="li_label_block">Слова памяти</div>
                <div id_label='5'class="li_label_block">Похожие могилы </div>
            </div>
            <div id_block='1' class="content_single_product">
                <div class="text_single_product">
                    @if (isset($life_story))
                        @if (count($life_story)>0)
                            @foreach ($life_story as $life_story_one)
                                <p>{{ $life_story_one->content }}</p>
                            @endforeach
                        @else
                        Нет информации
                        @endif
                    @endif
                </div>
            </div>
            <div id_block='2' class="content_single_product">
                <div class="text_single_product">
                    {!! $product->information !!}
                    @if($product->information!=null)
                        {!!$product->information!!}
                    @else
                        Нет информации
                    @endif
                </div>
            </div>
            <div id_block='3' class="content_single_product">
                <div class="text_single_product">
                    <div class="title_middle">Если вам дорога память об этом человеке, вы можете оставить здесь слова памяти</div>
                    <form action="{{ route('words-memory.add') }}"  method='post' enctype='multipart/form-data' class='form_words_memory'>
                        @csrf
                        <div class="input__wrapper">
                            <input style='display:none;' name="file" type="file" id="input__file_3" class="input input__file_2">
                            <label for="input__file_3" class="input__file-button">
                               <span class="input__file-button-text_2">Выберите изображение к словам благодарности (цветок, свеча, венок и пр.)</span>
                            </label>
                         </div>
                        <input type="hidden" value='{{ $product->id }}' name="product_id">
                        <div class="block_form_form_words_memory">
                            <div class="label_form_words_memory">Слова памяти</div>
                            <textarea name="content" id="" cols="30" rows="10" placeholder="Ваш текст"></textarea>
                            <button class='blue_btn' type='submit'>Сохранить</button>
                        </div>
                    </form>
                </div>
                @if (isset($memory_words))
                    <div class="ul_memory_words">
                        @if (count($memory_words)>0)
                            @foreach ($memory_words as $memory_word )
                                <div class="li_memory_words">
                                    <img src="{{$memory_word->urlImg()}}" alt="">
                                    <div class="block_li_memory_words">
                                        <div class="title_label">14.07.2024</div>
                                        <div class="text_middle">{{ $memory_word->content }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                        Нет информации
                        @endif
                    </div>
                @endif
            </div>
            <div id_block='4' class="content_single_product content_single_product_active">
                <div class="text_single_product">
                    <div class="title_middle">Выберите размер участка</div>
                        <form enctype='multipart/form-data' action="{{ route('burial.service.add',$product->id) }}"  method='get' class='form_services_add'>
                            @csrf
                            <select name="size" id="">
                                <option value="200x230">200x230</option>
                                <option value="200x240">200x240</option>
                                <option value="200x250">200x250</option>
                            </select>
                            @if (isset($services))
                                @if (count($services)>0)
                                    @foreach ($services as $service)
                                        <label class='checkbox'>
                                            <input price={{$service->getPriceForCemetery($product->cemetery->id)  }} type="checkbox" name="service[]" value='{{ $service->id }}'>
                                            <a href='{{ route('service.single',$service->id) }}'class="text_block_mini">{{ $service->title }}</a>
                                            <div class="title_middle">{{ $service->getPriceForCemetery($product->cemetery->id) }} ₽</div>
                                        </label>
                                    @endforeach
                                @endif
                            @endif
                           
                            <div class="flex_form_services_add">
                                <div class="block_total">
                                    <div class="text_block_mini">Итого услуг на сумму:</div>
                                    <div class="title_middle"><p>0</p> ₽</div>
                                </div>
                                <button type='submit' class="blue_btn">Заказать</button>
                            </div>
                        </form>
                </div>
            </div>
            <div id_block='5' class="content_single_product">
                <div class="similar_graves">
                    <div class="title_middle">Однофамильцы</div>
                    <div class="ul_similar_graves">
                        @if (isset($products_names))
                                @if (count($products_names)>0)
                                    @foreach ($products_names as $products_name)
                                        <div class="li_product">
                                            <div class="one_block_li_product">
                                                <img src="{{asset('storage/uploads_burials/'. $products_name->img )}}" alt="">
                                                <div class="btn_gray">{{ $products_name->who }}</div>
                                            </div>
                                            <div class="two_block_li_product">
                                                <div class="title_li decoration_on">{{ $products_name->surname }} {{ $products_name->name }} {{ $products_name->patronymic }}</div>
                                                <div class="mini_flex_li_product">
                                                    <div class="title_label">Даты захоронения:</div>
                                                    <div class="text_li">{{ $products_name->date_birth }}-{{ $products_name->date_death }}</div>
                                                </div>
                                                <div class="mini_flex_li_product">
                                                    <div class="title_label">Место захоронения:</div>
                                                    <div class="text_li">{{ $products_name->location_death }}</div>
                                                </div>
            
                                                <div class="flex_btn_li_product">
                                                    <a href='{{ route('burial.add',$products_name->id) }}'class="blue_btn">Получить координаты</a>
                                                    <a href='{{ $products_name->route() }}'class="btn_border_blue">Подробнее</a>
                                                    <a href='{{ route('favorite.add',$product->id) }}'class="btn_border_blue img_mini_star"><img src="{{ asset('storage/uploads/Star 1 (1).svg')}}" alt=""></a>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                        @endif
                    </div>
                </div>
                <div class="similar_graves">
                    <div class="title_middle">Умерли в эту дату</div>
                    <div class="ul_similar_graves">
                        @if (isset($products_dates))
                                @if (count($products_dates)>0)
                                    @foreach ($products_dates as $products_date)
                                        <div class="li_product">
                                            <div class="one_block_li_product">
                                                <img src="{{asset('storage/uploads_burials/'. $products_date->img )}}" alt="">
                                                <div class="btn_gray">{{ $products_date->who }}</div>
                                            </div>
                                            <div class="two_block_li_product">
                                                <div class="title_li decoration_on">{{ $products_date->surname }} {{ $products_date->name }} {{ $products_date->patronymic }}</div>
                                                <div class="mini_flex_li_product">
                                                    <div class="title_label">Даты захоронения:</div>
                                                    <div class="text_li">{{ $products_date->date_birth }}-{{ $products_date->date_death }}</div>
                                                </div>
                                                <div class="mini_flex_li_product">
                                                    <div class="title_label">Место захоронения:</div>
                                                    <div class="text_li">{{ $products_date->location_death }}</div>
                                                </div>
            
                                                <div class="flex_btn_li_product">
                                                    <a href='{{ route('burial.add',$products_date->id) }}'class="blue_btn">Получить координаты</a>
                                                    <a href='{{ $products_date->route() }}'class="btn_border_blue">Подробнее</a>
                                                    <a href='{{ route('favorite.add',$product->id) }}'class="btn_border_blue img_mini_star"><img src="{{ asset('storage/uploads/Star 1 (1).svg')}}" alt=""></a>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
       

        <div class="sidebar">
            <div class="btn_border_blue"  data-bs-toggle="modal" data-bs-target="#beautification_form"><img src="{{asset('storage/uploads/Frame (20).svg')}}" alt="">Облагородить могилу</div>
            <div class="cats_news">
                <div class="title_news">Изменить или уточнить</div>
                <div class="ul_cats_news">
                    <div  data-bs-toggle="modal" data-bs-target="#editing_burial_form"class="li_cat_news"><img src="{{asset('storage/uploads/Info_fill.svg')}}" >Основные данные</div>
                    <div class="li_cat_news open_monument_image_form"><img src="{{asset('storage/uploads/Img_box_fill.svg')}}" >Фотографии памятника</div>
                    <div class="li_cat_news open_personal_image_form"><img src="{{asset('storage/uploads/User_box_fill.svg')}}">Личные фотографии</div>
                    <div data-bs-toggle="modal" data-bs-target="#life_story_form"  class="li_cat_news"><img src="{{asset('storage/uploads/Paper_fill.svg')}}">История жизни</div>
                    <div id='open_words_memory' class="li_cat_news"><img src="{{asset('storage/uploads/Edit_fill.svg')}}">Слова памяти</div>
                    <div id='similar_burials' class="li_cat_news"><img src="{{asset('storage/uploads/Frame (23).svg')}}" >Похожие захоронения</div>
                </div>
            </div>
        </div>
    </div>
</section>
@include('footer.footer') 
