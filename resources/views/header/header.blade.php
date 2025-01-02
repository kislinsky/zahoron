<?php 
$user=user();
$city=selectCity();
?>


<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">


        <title>Главная</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

        <!-- Fonts -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

        <link rel="stylesheet" href="{{asset('css/style.css')}}">
        <link rel="stylesheet" href="{{asset('css/mobile.css')}}">

        <script src="https://api-maps.yandex.ru/1.1/index.xml" type="text/javascript"></script>
        <script src="https://api-maps.yandex.ru/2.1/?apikey=373ac95d-ec8d-4dfc-a70c-e48083741c72&lang=ru_RU"></script>

    </head>

<body >
<div class="margin_top_for_header"></div>
@include('components.all-forms-message')
@include('header.header-mobile')


<div class="bac_black city_question">
    <div class='message'>
        <div class="flex_title_message">
            <div class="title_middle">Выберите город</div>
            <div class="close_message">
                <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
            </div>
        </div>
        <div class="content_message">{!!  session("message_words_memory") !!}  </div>
        <div class="flex_btn">
            <div class="blue_btn close_message">Оставить по умолчанию</div>
            <div class="blue_btn close_message open_choose_city">Выбрать город</div>
        </div>
    </div>
</div>


<header class='header_main'>
   
    <div class="container">  
        <a class='logo' href='{{route('index')}}'>
            <img src='{{asset('storage/uploads/zahoron.svg')}}'>
        </a>
        @if (isset($page))
            <?php $page=$page;?>
        @else
            <?php $page=null;?>
        @endif
        <div class='pages'>
            <a href='{{route('index')}}'class="btn_bac_gray">Главная</a>
            <div id_city_selected='{{ $city->id }}' class="btn_bac_gray city_selected">
                <img src='{{ asset('storage/uploads/Group (22).svg') }}'>{{ $city->title }}
            </div>
            <div class="btn_bac_gray open_children_pages">
                Поиск могил
                <div class='children_pages'>
                    <a href='{{ route('page.search.burial.filter') }}'class="btn_bac_gray <?php if($page==1){echo ' active_label_product';}?>">Герои </a>
                    <a href='{{ route('page.search.burial.request') }}'class="btn_bac_gray <?php if($page==3){echo ' active_label_product';}?>">Заявка на поиск</a>
                </div>
                <img src='{{asset('storage/uploads/Vector 9 (1).svg')}}'>
            </div>
            <div class="btn_bac_gray open_children_pages">
                Облагораживание
                <div class='children_pages'>
                    <a href='{{ route('pricelist') }}'class="btn_bac_gray <?php if($page==8){echo ' active_label_product';}?>">Товары и услуги </a>
                    <a href='{{ route('marketplace') }}'class="btn_bac_gray <?php if($page==2){echo ' active_label_product';}?>">Маркетплейс</a>
                </div>
                <img src='{{asset('storage/uploads/Vector 9 (1).svg')}}'>
            </div>
           
        </div> 
       
        
        <div class='flex_icon_header'>
            <img class='open_big_header' src="{{asset('storage/uploads/menu-svgrepo-com.svg')}}" alt="">
            <div class='icon_header'><img src='{{asset('storage/uploads/Group 23.svg')}}'></div>
            <a href='{{ route('login') }}' class='icon_header icon_login'><img src='{{asset('storage/uploads/Group 1 (2).svg')}}'></a>
        </div>
    </div>
</header>

<div class="header_big">
    <div class="container">
        <div class="ul_pages_header_big">
            <div class="block_pages_header_big">
                <div class="title_li">Оформление заказа</div>
                <ul>
                  
                    <li>
                        <a class='text_black' href='{{ route('checkout.burial') }}'>Захоронений</a>
                    </li>
                    <li>
                        <a class='text_black' href='{{ route('checkout.service') }}'>Услуг</a>
                    </li>
                </ul>
            </div>
            <div class="block_pages_header_big">
                <div class="title_li">Места</div>
                <ul>
                    <li>
                        <a class='text_black' href='{{ route('cemeteries') }}'>Кладбища</a>
                    </li>
                    <li>
                        <a class='text_black' href='{{ route('mortuaries') }}'>Морги</a>
                    </li>
                    <li>
                        <a class='text_black' href='{{ route('columbariums') }}'>Колумабрии</a>
                    </li>
                    <li>
                        <a class='text_black' href='{{ route('crematoriums') }}'>Крематории</a>
                    </li>
                </ul>
            </div>
            <div class="block_pages_header_big">
                <div class="title_li">Организации</div>
                <ul>
                    <li>
                        <a class='text_black' href='{{ route('organizations') }}'>Каталог</a>
                    </li>
                    @if(@$user->role=='organization' || @$user->role=='organization-provider' || @$user->role=='admin')
                        <li>
                            <a class='text_black' href='{{ route('organizations.provider') }}'>Каталог поставщиков</a>
                        </li>
                    @endif
                </ul>
            </div>
            <div class="block_pages_header_big">
                <div class="title_li">Информация</div>
                <ul>
                    <li>
                        <a class='text_black' href='{{ route('our.products') }}'>Наши работы</a>
                    </li>
                    <li>
                        <a class='text_black' href='{{ route('news') }}'>Статьи</a>
                    </li>
                    <li>
                        <a class='text_black' href='{{ route('contacts') }}'>Контакты</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="mobile_header_mini_info">
    <div data-bs-toggle="modal" data-bs-target="#beautification_form" class="btn_border_blue">
        <img src="{{asset('storage/uploads/Frame (3).svg')}}" alt="">
        Облагородить 
    </div>
    <div class="flex_icon_header">
        <a href='{{ route('login') }}' class='icon_header'><img src='{{asset('storage/uploads/Group 1 (2).svg')}}'></a>
        <a class='gray_circle icon_header open_mobile_header' >
            <img src="{{asset('storage/uploads/Group 29.svg')}}" alt="">
        </a>
    </div>
</div>