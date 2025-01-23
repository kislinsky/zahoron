<?php $user=user();?>
<?php $city=selectCity();?>


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
        <link rel="stylesheet" href="{{asset('css/style-black-theme.css')}}">


        <script src="https://api-maps.yandex.ru/1.1/index.xml" type="text/javascript"></script>
        <script src="https://api-maps.yandex.ru/2.1/?apikey=373ac95d-ec8d-4dfc-a70c-e48083741c72&lang=ru_RU"></script>
    </head>

<body class='{{getTheme()}}'>
    
@include('components.all-forms-message')
@include('header.header-mobile-agency')


<header class='header_decoder header_agency'>
    <a class='logo' href='{{route('index')}}'>
        <img class='img_light_theme' src='{{asset('storage/uploads/zahoron.svg')}}'>
        <img class='img_black_theme' src="{{asset('storage/uploads/РИТУАЛреестр.svg')}}" alt="">    </a>
    
    @include('account.agency.components.header.choose-organization')

    <div class='flex_icon_header'>
        <div class='change_theme icon_header'><img class='img_light_theme' src='{{asset('storage/uploads/Group 23.svg')}}'><img class='img_black_theme' src='{{asset('storage/uploads/Group 23_black_theme.svg')}}'></div>
        <a href='{{ route('index') }}' class='icon_header icon_login'><img class='img_black_theme' src='{{asset('storage/uploads/Group 1_black_theme.svg')}}'><img class='img_light_theme' src='{{asset('storage/uploads/Group 1 (2).svg')}}'></a>
        <div class="text_black_bold">{{user()->name}}</div>
        <a class="text_black_bold" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Выйти</a>
        <a class='gray_circle icon_header open_mobile_header' >
            <img class='img_light_theme'src="{{asset('storage/uploads/Group 29.svg')}}" alt="">
            <img class='img_black_theme'src="{{asset('storage/uploads/Group 29 (1)_black.svg')}}" alt="">
        </a>
    </div>
</header>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>

<div class="mobile_choose_organiztion">
    @include('account.agency.components.header.choose-organization')
</div>
<script>

$( ".change_theme" ).on( "click", function() {
    $('body').toggleClass('black_theme')

    $.get("{{route('change-theme')}}", function (response) {
    });
})
    </script>