@include('header.header')
<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <div class="title">Актуальные цены на ритуальные услуги г. {{$city->title}}</div>
        </div>
        <img class='img_light_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        <img class='img_black_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1_black.svg')}}" alt="">
        
    </div>
</section>
<section class='main_index_block'>
    <div class="container">
        <div class="grid_two index_block_grid">
            <div class="gray_index_block_service">
                <div class="title_li">Организация похорон в городе {{$city->title}}</div>
                <div class="flex_text_service_index">
                    <div class="text_middle_index">Сравните цены на ритуальные
                        услуги в ритуальных агентствах
                        вашего города
                    </div>
                    <img src="{{asset('storage/uploads/Похороны-Photoroom 1.svg')}}" alt="" class="img_index_service">
                </div>
                <div class="grid_btn">
                    <div class="blue_btn open_funeral_arrangements border_radius_btn">Стоимость от {{minPriceCategoryProductOrganization('organizacia-pohoron')}} руб.</div>
                    <a href='{{route('organizations')}}'class="gray_btn">Каталог</A>
                </div>
            </div>

            <div class="gray_index_block_service dekstop_index_block">
                <div class="title_li">Отправка груза 200 в другой город</div>
                <div class="flex_text_service_index">
                    <div class="text_middle_index">Сравните стоимость услуг по отправке груза 200 в другой город выбрав агентство подходящее под Ваши запросы
                    </div>
                    <img class='img_light_theme'src="{{asset('storage/uploads/airplane 1.svg')}}" alt="" class="img_index_service">
                    <img class='img_black_theme'src="{{asset('storage/uploads/airplane 1_black.svg')}}" alt="" class="img_index_service">
                </div>
                <div class="grid_btn">
                    <div class="blue_btn open_shipping_200 border_radius_btn" >Стоимость от {{minPriceCategoryProductOrganization('otpravka-gruz-200')}} руб.</div>
                    <a href='{{route('organizations')}}'class="gray_btn">Каталог</A>
                </div>
            </div>

        </div>
        <div class="grid_two ">
            <div class="gray_index_block_service">
                <div class="title_li">Найти могилу в городе {{$city->title}}</div>
                <div class="flex_text_service_index">
                    <div class="text_middle_index">Найдите могилу и закажите уход за ней</div>
                    <img style='max-width:117px;'src="{{asset('storage/uploads/Похороны-Photoroom 2.svg')}}" alt="" class="img_index_service">
                </div>
                <form method='get' action="{{route('search.burial')}}" class='index_search'>
                    @csrf
                    <div class="grid_btn">
                        <input class='blue_input'type="text" name='surname' placeholder='Фамилия'>
                        <input class='blue_input'type="text" name='name' placeholder='Имя'>
                    </div>
                    <div class="grid_btn">
                        <input class='blue_input'type="text" name='patronymic' placeholder='Отчество'>
                        <button class='blue_btn border_radius_btn' type='submit'>Найти</button>
                    </div>  
                </form>
            </div>
            <div class="grid_two mobile_grid_index">
                    <div class="gray_index_block_service">
                        <a href='{{ route('mortuaries') }}' class="title_li">Морги в г. {{$city->title}}</a>
                        <div class="flex_text_service_index">
                            <a href='{{ route('mortuaries') }}' class="title_li title_li_mobile">Морги в г. {{$city->title}}</a>

                            <div class="mini_text">Узнайте информацию по
                                умершему</div>
                            <img class='img_light_theme mini_index_img'src="{{asset('storage/uploads/mortuary.svg')}}" alt="" class="img_index_service">
                            <img class='img_black_theme mini_index_img'src="{{asset('storage/uploads/menu 1 (1)_black.svg')}}" alt="" class="img_index_service">
                            
                        </div>
                        <div class="blue_btn border_radius_btn" data-bs-toggle="modal" data-bs-target="#dead_form">узнать</div>
                    </div>
                    <div class="gray_index_block_service">
                        <a href='{{ route('cemeteries') }}' class="title_li">Кладбища 
                            г. {{$city->title}}</a>
                        <div class="flex_text_service_index">
                            <a href='{{ route('cemeteries') }}' class="title_li title_li_mobile">Кладбища 
                                г. {{$city->title}}</a>
                            <div class="mini_text">Узнайте рабочий режим кладбища, позвоните или
                                узнайте как
                                добраться.</div>
                                <img class='img_light_theme mini_index_img'src="{{asset('storage/uploads/menu 1.svg')}}" alt="" >
                                <img class='img_black_theme mini_index_img'src="{{asset('storage/uploads/menu 1 (2)_black.svg')}}" alt="" >
                            </div>
                        <a href='{{ route('cemeteries') }}' class="blue_btn border_radius_btn">Позвонить</a>
                    </div>
                </div>
            </div>

        <div class="grid_two">
            <div class="grid_two dekstop_index_block">
                <div class="gray_index_block_service">
                    <a href='{{route('marketplace.category','pominal-nyh-obedy')}}' class="title_li">Заказать поминки
                        г. {{$city->title}}</a>
                    <div class="flex_text_service_index">
                        <div class="mini_text">Получите ценовое
                            предложения 
                            с разных кафе
                            вашего района 
                            в г. Иваново</div>
                            <img class='img_light_theme mini_index_img' src="{{asset('storage/uploads/Group_index.svg')}}" alt="" >
                            <img class='img_black_theme mini_index_img' src="{{asset('storage/uploads/menu 1 (3)_black.svg')}}" alt="" >
                        </div>
                    <div class="blue_btn border_radius_btn" data-bs-toggle="modal" data-bs-target="#memorial_form">Оформить заказ</div>
                </div>
                <div class="gray_index_block_service">
                    <a href='{{route('marketplace.category','organizacia-kremacii')}}' class="title_li">Организация 
                        кремации г. {{$city->title}}</a>
                    <div class="flex_text_service_index">
                        <div class="mini_text">Вам предоставят цены на кремацию от более 10 ритуальных агентств Иваново для выбора лучшего варианта.</div>
                        <img src="{{asset('storage/uploads/Group_cemetery.svg')}}" alt="" class="img_light_theme img_index_service">
                        <img src="{{asset('storage/uploads/_index_block_vblack.svg')}}" alt="" class="img_black_theme img_index_service">
                    </div>
                    <div class="blue_btn border_radius_btn open_organization_cremation">Узнать цены</div>
                </div>
            </div>

            <div class="gray_index_block_service">
                <div class="title_li">Маркетплейс с ценами на все виды ритуальных услуг в г. {{$city->title}}</div>
                <div class="flex_text_service_index">
                    <div class="mini_text">Сравнивайте и покупайте памятники, 
                        оградки,столики и лавочки, заказывайте 
                        поминки ивсе, что связано с ритуальными
                         услугами в г. Иваново.</div>
                    <img src="{{asset('storage/uploads/437956-mogila-narisovannaia-24-Photoroom 1.svg')}}" alt="" class="img_index_service">
                </div>
                <div class="flex_btn">
                    <div class="blue_btn border_radius_btn" data-bs-toggle="modal" data-bs-target="#beautification_form">Запрос цен</div>
                    <a href='{{route('marketplace.category','pamatniki')}}'class="blue_btn border_radius_btn">Памятники</a>
                    <a href='{{route('marketplace.category','ogradki')}}'class="blue_btn border_radius_btn">Оградки</a>
                </div>
            </div>
        

            <div class="grid_two mobile_index_block_grid">
                <div class="gray_index_block_service">
                    <a href='{{route('marketplace.category','pominal-nyh-obedy')}}' class="title_li">Заказать поминки
                        г. {{$city->title}}</a>
                    <div class="flex_text_service_index">
                        <a href='{{route('marketplace.category','pominal-nyh-obedy')}}' class="title_li title_li_mobile">Заказать поминки
                            г. {{$city->title}}</a> 
                        <div class="mini_text">Получите ценовое
                            предложения 
                            с разных кафе
                            вашего района 
                            в г. Иваново</div>
                        <img class='mini_index_img' src="{{asset('storage/uploads/Group_index.svg')}}" alt="" class="img_index_service">
                    </div>
                    <div class="blue_btn border_radius_btn" data-bs-toggle="modal" data-bs-target="#memorial_form">Оформить заказ</div>
                </div>
                <div class="gray_index_block_service">
                    <a href='{{route('marketplace.category','organizacia-kremacii')}}' class="title_li">Организация 
                        кремации г. {{$city->title}}</a>
                    <div class="flex_text_service_index">
                        <a href='{{route('marketplace.category','organizacia-kremacii')}}' class="title_li title_li_mobile">Организация 
                            кремации г. {{$city->title}}</a> 
                        <div class="mini_text">Вам предоставят цены на кремацию от более 10 ритуальных агентств Иваново для выбора лучшего варианта.</div>
                        <img src="{{asset('storage/uploads/Group_cemetery.svg')}}" alt="" class="img_index_service">
                    </div>
                    <div class="blue_btn border_radius_btn open_organization_cremation">Узнать цены</div>
                </div>
            </div>


            <div class="gray_index_block_service mobile_index_block">
                <div class="flex_text_service_index">
                    <div class="title_li">Отправка груза 200 в другой город</div>

                    <img src="{{asset('storage/uploads/airplane 1.svg')}}" alt="" class="img_index_service">
                </div>
                <div class="grid_btn">
                    <div class="blue_btn open_shipping_200 border_radius_btn" >Стоимость от {{minPriceCategoryProductOrganization('otpravka-gruz-200')}} руб.</div>
                    <a href='{{route('organizations')}}'class="gray_btn">Каталог</A>
                </div>
            </div>
        </div>
    </div>
</section>

@include('components.rating-funeral-agencies-prices')

<section class="block">
    <div class="container">
        <div class="grid_two index_block_grid">
            <img src="{{asset('storage/uploads/002-spisok-uslug-2 1.png')}}" alt="" class="img_text_block">
            <div class="text_block_index">
                <div class="title_text_block">Получите расчет стоимости ритуальных
                    услуг от 10 проверенных организаций 
                    без дополнительных услуг
                </div>
                <div class="blue_btn open_shipping_200">Сэкономить до 20 000 руб.</div>
            </div>
        </div>
    </div>
</section>



@include('components.funeral-service-marketplace')

@include('components.rating-uneral-bureaus-raves-prices')

@include('components.monuments-grave')


<section class="block">
    <div class="container">
        <div class="grid_two index_block_grid">
            <img src="{{asset('storage/uploads/002-spisok-uslug-2 2.png')}}" alt="" class="img_text_block">
            <div class="text_block_index">
                <div class="title_text_block">Получите прямой расчёт
                    от 10 проверенных ритуальных агентств по низким ценам
                </div>
                <div class="blue_btn" data-bs-toggle="modal" data-bs-target="#beautification_form">Получить расчет</div>
            </div>
        </div>
    </div>
</section>

@include('components.rating-establishments-providing-halls-holding-commemorations')

@include('components.memorial-dinners-marketplace')

@include('components.memorial-hall-marketplace')

{{-- <section class="speczialist">
    <div class="container">
        <div class="ul_speczialist">
            <div class="index_block_speczialist">
                <div class="gray_block_speczialist">
                    <div class="title_news title_blue">Все материалы на сайте проверены экспертом</div>
                    <img src="{{ asset('storage/uploads/galochka.svg') }}" alt="">
                </div>
                <div class="content_speczialist_index">
                    <img src="{{ asset('storage/uploads/photo_spec.svg') }}" alt="" class="img_speczialist">
                    <div>
                        <a href='{{ route('speczialist') }}'class="title_news">Кислинский Александр Валерьевич</a>
                        <div class="text_li">Специалист по вопросам похоронного дела</div>
                    </div>
                </div>
            </div>
            <div class="block_count_speczialist">
                <div class="title_big_blue">5000+</div>
                <div class="text_black_bold">Облагорожено захоронений</div>
            </div>
            <div class="block_count_speczialist">
                <div class="title_big_blue">15 000+</div>
                <div class="text_black_bold">Уборок могил</div>
            </div>
        </div>
    </div>
</section> --}}

{{-- 
<section class='professional_care'>
    <div class="container">
        <div class="title">Профессиональный уход<br> за могилами</div>
        <div class="text_block">Мы предлагаем профессиональный уход за могилами, уборку кладбища и уход за захоронениями. У нас вы найдете доступные цены и качественное обслуживание.</div>
        <div class="ul_care">
        
            <div class="li_care">
                <div class="number_li_care">1</div>
                <div class="title_li">Подготовка участка</div>
                <div class="text_li">Подравниваем траву, избавляемся от сорняков и увядших растений, сгребаем опавшие листья и ветки, вывозим весь накопившийся мусор. Разрыхляем и увлажняем почву.</div>
            </div>
            <div class="li_care">
                <div class="number_li_care">2</div>
                <div class="title_li">Чистим захоронение</div>
                <div class="text_li">Отмываем памятники, украшения и ограду от налипших пыли и мусора. Бережно счищаем с могильной плиты глубоко въевшуюся грязь и ржавчину.</div>
            </div>
            <div class="li_care">
                <div class="number_li_care">3</div>
                <div class="title_li">Косметический ремонт</div>
                <div class="text_li">Обновляем стёршиеся надписи на памятниках и надгробных плитах. Цементируем щели и зазоры на стыках. Возвращаем отпавшие фрагменты металлических объектов на место.</div>
            </div>
            <div class="li_care">
                <div class="number_li_care">4</div>
                <div class="title_li">Защита захоронения</div>
                <div class="text_li">Обрабатываем каменные и металлические объекты водооталкивающими и биозащитными средствами. Благодаря этому, они будут лучше противиться воздействию осадков и палящих солнечных лучей.</div>
            </div>
            <div class="li_care">
                <div class="number_li_care">5</div>
                <div class="title_li">Приносим цветы</div>
                <div class="text_li">В качестве финального штриха, украшаем могилу живыми или искусственными цветами. У нас большой выбор цветов (венков в том числе), так что вы непременно найдёте что-то по сердцу.</div>
            </div>
            <div class="blue_care">
                <div class="title_li">Оставить заявку онлайн</div>
                <div class="text_li">Заполните основные формы, дождитесь звонка менеджера компании, уточните детали и получите ответы на возникшие вопросы. Прайс с ценами и описанием услуг на уборку и благоустройство представлен в одноименном разделе сайта.</div>
                <div class="white_btn" data-bs-toggle="modal" data-bs-target="#beautification_form">Облагородить могилу</div>
            </div>
        </div>
    </div>
</section> --}}


@include('components.rewies') 

@include('components.reviews-funeral-organization') 

@include('components.map-cemeteries') 

@include('components.map-morgues') 



{{view('components.news',compact('news'))}}




@include('components.faq') 

{{view('components.news-video',compact('news_video'))}}

@if($city->text_about_project!=null)
    <section class="about_company bac_gray">
        <div class="container">
            <div class="title">О проекте "Цены на ритуальные услуги в г. {{$city->title}}</div>
            <div class="content_block">{!! $city->text_about_project !!}</div>

        </div>
    </section>
@endif


@include('components.cats-product') 


@if($city->text_how_properly_arrange_funeral_services!=null)
    <section class="about_company bac_gray">
        <div class="container">
            <div class="title">Как правильно оформить ритуальные услуги в г. {{$city->title}}</div>
            <div class="content_block">{!!$city->text_how_properly_arrange_funeral_services !!}</div>

        </div>
    </section>
@endif





@include('footer.footer') 


