@include('header.header')
{{view('components.shema-org.service',compact('service'))}}

<?php 

use App\Models\Burial;
use App\Models\Service;

?>
<section class="order_page bac_gray">
    <div class="container order_page_search">
        <div class="content_order_page">
            <div class="index_title">{{ $service->title }}</div>    
        </div>
        <img class='img_light_theme rose_checkout'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        <img class='img_black_theme rose_checkout'src="{{asset('storage/uploads/rose-with-stem 1_black.svg')}}" alt=""> 
    </div>
</section>


<section class="single_service">
    <div class="container">
        <div class="title">
            {{ $service->title }} могилы на <a href="{{ route('cemeteries.single',$cemetery->id) }}">{{ $cemetery->title }}</a> в городе {{ $city->title }} {{ $edge->title }} области
        </div>
        @if($service->text_under_title!=null)
            <div class="text_block">
                {!! $service->text_under_title !!}
            </div>
        @endif
        @if($service->video_1!=null)
            <div class="video_service">
                <img class='btn_play_video' src="{{asset('storage/uploads/Group 34.svg')}}" alt="">
                <video controls src="{{asset('storage/uploads_service/'. $service->video_1 )}}"></video>
            </div>
        @endif
       
        <div class="single_flex_btn">
            <div class="blue_btn">Заказать возложение</div>
            <div class="title_middle center_text">Цена {{$service->price}} руб.</div>
        </div>
        @if($service->text_under_video_1!=null)
        <div class="text_block border_gray">
            {!! $service->text_under_video_1 !!}
        </div>
    @endif
        <div class="ul_advanyages_service">
            <div class="li_advantage_service">
                <img src="{{asset('storage/uploads/Line 2 (1).svg')}}" alt="">
                <div class="title_news">Быстрая реакция на заявку</div>
                <div class="title_rewies">70% заказов выполняем
                    в течение 3 дней, 
                    сложные - не более 20 дней</div>
            </div>
            <div class="li_advantage_service">
                <img src="{{asset('storage/uploads/Frame (35).svg')}}" alt="">
                <div class="title_news">Косметический ремонт</div>
                <div class="title_rewies">По желанию заказчика обновляем могилу, в случае мелких разрушений</div>
            </div>
            <div class="li_advantage_service">
                <img src="{{asset('storage/uploads/Subtract (4).svg')}}" alt="">
                <div class="title_news">Удалённый уход за могилой</div>
                <div class="title_rewies">Закажите уборку могилы
                    из любой точки земного шара</div>
            </div>
        </div>
        @if($service->text_sale!=null)
            <div class="text_block border_gray">
                {!! $service->text_sale !!}
            </div>
        @endif
        <div class="single_flex_btn">
            <div class="blue_btn">Заказать возложение</div>
            <div class="title_middle center_text">Цена {{$service->price}} руб.</div>
        </div>
      

       
        <div class="block_single_cemetery">
            <div id='mute-video'class="title_our_works">Кладбище <a href="{{ route('cemeteries.single',$cemetery->id) }}">"{{ $cemetery->title }}"</a> в городе {{ $city->title }}</div>
            <div id="map_cemetery_single" style="width: 100%; height: 600px"></div>
        </div>

        @include('forms.search-form') 

        @include('components.cats-product') 

    </div>
</section>


<script>
    ymaps.ready(init);

function init() {
    var myMap = new ymaps.Map("map_cemetery_single", {
            center: [{{  $cemetery->width}}, {{$cemetery->longitude}}],
            zoom: 10
        }, {
            searchControlProvider: 'yandex#search'
        });

      myMap.geoObjects
        .add(new ymaps.Placemark([{{ $cemetery->width }}, {{ $cemetery->longitude }}], {
            balloonContent: '{{ $cemetery->title }}',
            iconCaption:  '{{ $cemetery->title }}'
        },));
}
</script>

@include('footer.footer')
