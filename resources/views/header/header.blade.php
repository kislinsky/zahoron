<?php 
$user=user();
$city=selectCity();
use Artesaos\SEOTools\Facades\SEOTools;
?>


<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">


        {!! SEOTools::generate() !!}
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

        <!-- Fonts -->
        <link  defer href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
        <link defer rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
        <link  defer rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

        <link defer rel="stylesheet" href="{{asset('css/style.css')}}">
        <link defer rel="stylesheet" href="{{asset('css/style-black-theme.css')}}">
        <link defer rel="stylesheet" href="{{asset('css/mobile.css')}}">
        <link defer rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/css/lightgallery-bundle.min.css" />

        <script defer src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/lightgallery.min.js"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/plugins/zoom/lg-zoom.min.js"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/plugins/thumbnail/lg-thumbnail.min.js"></script>
        <script  src="https://api-maps.yandex.ru/2.1/?apikey=373ac95d-ec8d-4dfc-a70c-e48083741c72&lang=ru_RU"></script>

        {!! get_acf(20,'header') !!}
    </head>

<body class='{{getTheme()}}'>
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
            <img class='img_light_theme' src='{{asset('storage/uploads/zahoron.svg')}}'>
            <img class='img_black_theme' src="{{asset('storage/uploads/РИТУАЛреестр.svg')}}" alt="">
        </a>
        @if (isset($page))
            <?php $page=$page;?>
        @else
            <?php $page=null;?>
        @endif
        <div class='pages'>
            <div id_city_selected='{{ $city->id }}' class="btn_bac_gray city_selected">
                <img class='img_light_theme'src='{{ asset('storage/uploads/Group (22).svg') }}'>
                <img class='img_black_theme'src='{{ asset('storage/uploads/Group_black_theme.svg') }}'>
                {{ $city->title }}
            </div>

            <div class="btn_bac_gray open_children_pages">
               Ритуальные услуги
                <div class='children_pages'>
                    <a href='{{ route('organizations.category','organizacia-pohoron') }}'class="btn_bac_gray">Ритуальные агенства </a>
                    <a href=''class="btn_bac_gray">Ритуальные товары, услуги </a>
                </div>
                <img class='img_light_theme' src='{{asset('storage/uploads/Vector 9 (1).svg')}}'>
                <img class='img_black_theme' src='{{asset('storage/uploads/Vector 9_black.svg')}}'>
            </div>

            <div class="btn_bac_gray open_children_pages">
               Ритуальные обьекты
                <div class='children_pages'>
                    <a href='{{ route('cemeteries') }}'class="btn_bac_gray">Кладбища </a>
                    <a href='{{ route('mortuaries') }}'class="btn_bac_gray">Морги </a>
                   
                </div>
                <img class='img_light_theme' src='{{asset('storage/uploads/Vector 9 (1).svg')}}'>
                <img class='img_black_theme' src='{{asset('storage/uploads/Vector 9_black.svg')}}'>
            </div>


            <div class="btn_bac_gray open_children_pages">
                Поиск могил
                <div class='children_pages'>
                    <a href='{{ route('search.burial') }}'class="btn_bac_gray <?php if($page==11){echo ' active_label_product';}?>">Поиск </a>
                    <a href='{{ route('page.search.burial.filter') }}'class="btn_bac_gray <?php if($page==1){echo ' active_label_product';}?>">Герои </a>
                    <a href='{{ route('page.search.burial.request') }}'class="btn_bac_gray <?php if($page==3){echo ' active_label_product';}?>">Заявка на поиск</a>
                </div>
                <img class='img_light_theme' src='{{asset('storage/uploads/Vector 9 (1).svg')}}'>
                <img class='img_black_theme' src='{{asset('storage/uploads/Vector 9_black.svg')}}'>
 
            </div>
           

            <div class="btn_bac_gray open_children_pages">
               Информация
                <div class='children_pages'>
                    <a href='{{ route('our.products') }}'class="btn_bac_gray">Наши работы </a>
                    <a href='{{ route('news') }}'class="btn_bac_gray">Статьи </a>
                    <a href='{{ route('contacts') }}'class="btn_bac_gray">Контакты </a>
                   
                </div>
                <img class='img_light_theme' src='{{asset('storage/uploads/Vector 9 (1).svg')}}'>
                <img class='img_black_theme' src='{{asset('storage/uploads/Vector 9_black.svg')}}'>
            </div>
           
        </div> 
       
        
        <div class='flex_icon_header'>
           <div class='change_theme icon_header'><img class='img_light_theme' src='{{asset('storage/uploads/Group 23.svg')}}'><img class='img_black_theme' src='{{asset('storage/uploads/Group 23_black_theme.svg')}}'></div>
            <a href='{{ route('login') }}' class='icon_header icon_login'><img class='img_black_theme' src='{{asset('storage/uploads/Group 1_black_theme.svg')}}'><img class='img_light_theme' src='{{asset('storage/uploads/Group 1 (2).svg')}}'></a>
        </div>
    </div>
</header>


<div class="mobile_header_mini_info">
    @if(versionProject())
        <div class="btn_border_blue">
            <img src="{{asset('storage/uploads/Frame (3).svg')}}" alt="">
            Облагородить 
        </div>
    @else
        <div data-bs-toggle="modal" data-bs-target="#beautification_form" class="btn_border_blue">
            <img src="{{asset('storage/uploads/Frame (3).svg')}}" alt="">
            Облагородить 
        </div>
    @endif
    
    <div class="flex_icon_header">
        <a href='{{ route('login') }}' class='icon_header icon_login'><img class='img_black_theme' src='{{asset('storage/uploads/Group 1_black_theme.svg')}}'><img class='img_light_theme' src='{{asset('storage/uploads/Group 1 (2).svg')}}'></a>
        <a class='gray_circle icon_header open_mobile_header' >
            <img class='img_light_theme'src="{{asset('storage/uploads/Group 29.svg')}}" alt="">
            <img class='img_black_theme'src="{{asset('storage/uploads/Group 29 (1)_black.svg')}}" alt="">
        </a>
    </div>
</div>
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>

<script>

$( ".change_theme" ).on( "click", function() {
    $('body').toggleClass('black_theme')

    $.get("{{route('change-theme')}}", function (response) {
    });
})
</script>