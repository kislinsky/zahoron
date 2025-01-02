<?php 
use App\Models\Product;
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

<body>


<header>
   
    <div class="container">  
        <a class='logo' href='/'>
            <img src='{{asset('storage/uploads/zahoron.svg')}}'>
        </a>
        @if (isset($page))
            <div class='pages'>
                <a href='{{ route('home') }}'class="btn_bac_gray <?php if($page==1){echo ' active_label_product';}?>">Главная </a>
                <a href='{{ route('account.agent.services.index') }}'class="btn_bac_gray <?php if($page==3){echo ' active_label_product';}?>">Уборка захоронений</a>
                <a href='{{ route('account.agent.settings') }}'class="btn_bac_gray <?php if($page==5){echo ' active_label_product';}?>">Настройки</a>
                <a href='#'class="btn_bac_gray">Чат</a>
            </div>
        @else
            <div class='pages'>
                <a href='{{ route('home') }}'class="btn_bac_gray">Главная </a>
                <a href='{{ route('account.agent.services.index') }}'class="btn_bac_gray">Уборка захоронений</a>
                <a href='{{ route('account.agent.settings') }}'class="btn_bac_gray">Настройки</a>
                <a href='#'class="btn_bac_gray">Чат</a>
            </div>
        @endif
        <div class='flex_icon_header'>
            <div class='icon_header'><img src='{{asset('storage/uploads/Group 23.svg')}}'></div>
            <a href='/login' class='icon_header'><img src='{{asset('storage/uploads/Group 1 (2).svg')}}'></a>
            <a class="no_bac_btn logout" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Выйти</a>
            
        </div>
    </div>
</header>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>
@if(session("message_words_memory"))
    <div class="bac_black">
        <div class='message'>
            <div class="flex_title_message">
                <div class="title_middle">Заявка принята</div>
                <div class="close_message">
                    <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                </div>
            </div>
            <div class="content_message">{!!  session("message_words_memory") !!}  </div>
            <div class="blue_btn close_message">OK</div>
        </div>
    </div>
@endif



@if(session("message_cart"))
    <div class="bac_black">
        <div class='message'>
            <div class="flex_title_message">
                <div class="title_middle">{!!  session("message_cart") !!} </div>
                <div class="close_message">
                    <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                </div>
            </div>
            <div class="blue_btn close_message">OK</div>
        </div>
    </div>
@endif

@if(session("message_order_burial"))
    <div class="bac_black">
        <div class='message'>
            <div class="flex_title_message">
                <div class="title_middle">Заказ оформлен</div>
                <div class="close_message">
                    <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                </div>
            </div>
            <div class="content_message">{{ session("message_order_burial") }}</div>
            <div class="blue_btn close_message">OK</div>
        </div>
    </div>
@endif

@if(session("error"))
    <div class="bac_black">
        <div class='message'>
            <div class="flex_title_message">
                <div class="title_middle">Ошибка</div>
                <div class="close_message">
                    <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                </div>
            </div>
            <div class="content_message">{{ session("error") }}</div>
            <div class="blue_btn close_message">OK</div>
        </div>
    </div>
@endif


