@include('header.header')

<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            {{view('organization.components.catalog.title-page',compact('title_h1','city','category','category_main'));}}
        </div>
        <img class='img_light_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        <img class='img_black_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1_black.svg')}}" alt="">    </div>
</section>

<div class="html_navigation">
    {{view('components.navigation',compact('pages_navigation'))}}
</div>

<section class="organization_marketplace">
    <div class="container">
        <div class="grid_product_two">
            <div class="one_block_market">
                <div class="block_table_price_orgniaztions">
                    {{view('organization.components.catalog.prices',compact('price_min','price_middle','price_max','city','category'));}}
                   
                </div>

                {{view('organization.components.catalog.filters',compact('filter_work','sort','cemeteries','cemetery_choose','districts','district_choose','cats','category'))}}
                
                <div class="ul_organizaiotns">
                    {{view('organization.components.catalog.organizations-show',compact('organizations_category'))}}
                </div>
                

            </div>
            <div class="sidebar">
                {{view('organization.components.catalog.sidebar',compact('cats','category'))}}
            </div>
        </div>
</section>

<div class="map_organizations">
    {{view('organization.components.catalog.map-cats',compact('category','organizations_category','city'));}}
</div>

<section>
    <div class="container">
        <div class="gos_block gos_block_1">
            <img src="{{asset('storage/uploads/image 29.png')}}" alt="">  
            <div class="content_gos_block">
                <div class="title_blue_big">Государственные выплаты <span class='title_green_big'>+ 13500 рублей</span></div>    
                <div class="text_gray">Выплаты производятся умершиим не работающим пенсионерам</div>
            </div>      
        </div>
    </div>
</section>



@include('components.rating-funeral-agencies-prices')

<section class="block">
    <div class="container">
        <div class="grid_two mobile_block_info_1_grid">
            <img src="{{asset('storage/uploads/002-spisok-uslug-2 1.png')}}" alt="" class="img_text_block">
            <div class="text_block_index">
                <div class="title_text_block">Получите расчет стоимости ритуальных
                    услуг от 10 проверенных организаций 
                    без дополнительных услуг
                </div>
                @if(versionProject())
                    <a href='{{ route('organizations') }}'class="blue_btn">Сэкономить до 20 000 руб.</a>
                @else
                    <div class="blue_btn open_shipping_200">Сэкономить до 20 000 руб.</div>
                @endif
            </div>
        </div>
    </div>
</section>



@include('components.funeral-service-marketplace')

@include('components.rating-uneral-bureaus-raves-prices')

@include('components.monuments-grave')


<section class="block">
    <div class="container">
        <div class="grid_two mobile_block_info_1_grid">
            <img src="{{asset('storage/uploads/002-spisok-uslug-2 2.png')}}" alt="" class="img_text_block">
            <div class="text_block_index">
                <div class="title_text_block">Получите прямой расчёт
                    от 10 проверенных ритуальных агентств по низким ценам
                </div>
                @if(versionProject())
                    <a href='{{ route('organizations.category','pamatniki') }}'class="blue_btn">Получить расчет</a>
                @else
                    <div class="blue_btn" data-bs-toggle="modal" data-bs-target="#beautification_form">Получить расчет</div>
                @endif
            </div>
        </div>
    </div>
</section>

@include('components.rating-establishments-providing-halls-holding-commemorations')

@include('components.memorial-dinners-marketplace')

@include('components.memorial-hall-marketplace')

@include('components.rewies') 

@include('components.reviews-funeral-organization') 

@include('components.map-morgues') 

@include('components.map-cemeteries') 

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

@include('components.cats-organization') 


@if($city->text_how_properly_arrange_funeral_services!=null)
    <section class="about_company bac_gray">
        <div class="container">
            <div class="title">Как правильно оформить{{ $category->title }} в г. {{$city->title}}</div>
            <div class="content_block">{!!$category->content !!}</div>

        </div>
    </section>
@endif
@include('footer.footer') 

@include('organization.components.ajax.main') 
