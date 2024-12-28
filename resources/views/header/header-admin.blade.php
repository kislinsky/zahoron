<?php $user=user();?>


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
    
@include('components.all-forms-message')


<header class='header_decoder'>
    <a class='logo' href='{{route('index')}}'>
        <img src='{{asset('storage/uploads/zahoron.svg')}}'>
    </a>
    
    <div class="item_decoder">
        <div class="logo_decoder">
            @if($user->icon!=null)
                <img src='{{asset('storage/uploads_decoder/'.$user->icon)}}'>
            @else
                <img src='{{asset('storage/uploads/ImgAbout.png')}}'>
            @endif
        </div>
        <div class="title_memorial_dinner">{{$user->surname}} {{$user->name}} {{$user->patronymic}}</div>
    </div>
    <div class='flex_icon_header'>
        <div class='icon_header'><img src='{{asset('storage/uploads/Group 23.svg')}}'></div>
        <a href='/login' class='icon_header'><img src='{{asset('storage/uploads/Group 1 (2).svg')}}'></a>
        <a class="no_bac_btn logout" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Выйти</a>
    </div>
</header>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>