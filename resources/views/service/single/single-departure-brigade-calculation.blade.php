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
            Поиск могилы без присутствия заказчика на кладбищах в городе {{ $city->title }} , с предоставление сметы по облагораживанию захоронения с видео и фотоотчёта заказчику.
        </div>
        @if($service->text_under_title!=null)
            <div class="text_block">
                {!! $service->text_under_title !!}
            </div>
        @endif
        

        <div class="ul_advanyages_service">
            <div class="li_advantage_service">
                <img src="{{asset('storage/uploads/Star 1.svg')}}" alt="">
                <div class="title_news">Поиск могилы по
                    геолокации заказчика
                    приобретенной на сайте</div>
                <div class="title_rewies">Наши дорогие и близкие заслуживают чего. Поддерживайте могилу в состоянии,
                    не прилагая усилий</div>
            </div>
            <div class="li_advantage_service">
                <img src="{{asset('storage/uploads/Subtract.svg')}}" alt="">
                <div class="title_news">Исследование захоронения
                    на момент реставрации и
                    облагораживания</div>
                <div class="title_rewies">Качественная покраска оград и других металлических объектов на кладбищах позволит вам
                    забыть о ремонте могилы на 5 лет</div>
            </div>
            <div class="li_advantage_service">
                <img src="{{asset('storage/uploads/Subtract (1).svg')}}" alt="">
                <div class="title_news">Предоставление сметы
                    работ по облагораживанию 
                    и видео, фотообзора</div>
                <div class="title_rewies">Какой краской лучше покрыть оп
                    могиле? Как правильно удалить ржавчину. Предоставьте это нам
                     - и пол результат через 1-2 дня!</div>
            </div>
        </div>
        @if($service->img_structure!=null)
            <img class='img_structure'src="{{asset('storage/uploads_service/'.$service->img_structure)}}" alt="">
        @endif
        <div class="single_flex_btn">
            <div class="blue_btn">Заказать выезд</div>
            <div class="title_middle center_text">от {{$service->price}} руб.</div>
        </div>
      
        @if (count($imgs_service)>0)
            <div class="ul_our_products">
                @foreach ($imgs_service as $img_service )
                    <div class="li_our_work">
                        <div class="title_before_our_works">До уборки</div>
                        <div class="title_after_our_works">После уборки</div>
                        <img src="{{asset('storage/uploads_service/'. $img_service->img_before )}}" alt="">
                        <img src="{{asset('storage/uploads_service/'. $img_service->img_after )}}" alt="">
                    </div>
                @endforeach
            </div>
        @endif
        @if($service->video_1!=null)
            <div class="video_service">
                <img class='btn_play_video' src="{{asset('storage/uploads/Group 34.svg')}}" alt="">
                <video controls src="{{asset('storage/uploads_service/'. $service->video_1 )}}"></video>
            </div>
        @endif

        @if(count($stages_service)>0)
            <?php $k=1;?>
            <div class="text_block border_gray painting_fence">
                <div class="ul_stages_service">
                    @foreach ($stages_service as $stage_service)
                        <div class="li_stage_service">
                            <div class="img_service_stage">
                                <img src="{{ asset('storage/uploads_stages_service/'.$stage_service->img) }}" alt="">
                                <div class="count_service">{{ $k++ }}</div>
                            </div>
                            <div class="content_stage_service">
                                <div class="title_li">{{ $stage_service->title }}</div>
                                <div class="text_block">{{ $stage_service->content }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        <div class="single_flex_btn">
            <div class="blue_btn">Заказать выезд</div>
            <div class="title_middle center_text">от {{$service->price}} руб.</div>
        </div>
        @if($service->text_sale!=null)
            <div class="text_block border_gray">
                <div class="title_li">Услуги по покраске ограды </div>

                {!! $service->text_sale !!}
            </div>
         @endif
   
         {{view('service.components.reviews',compact('reviews'))}}


       
        @if($service->text_under_img!=null)
            <div class="text_block border_gray">
                <div class="title_li">Стоимость покраски оградки </div>
                {!! $service->text_under_img !!}
            </div>
        @endif
       
        {{-- <div class="block_single_cemetery">
            <div id='mute-video'class="title_our_works">Кладбище <a href="{{ route('cemeteries.single',$cemetery->id) }}">"{{ $cemetery->title }}"</a> в городе {{ $city->title }}</div>
            <div id="map_cemetery_single" style="width: 100%; height: 600px"></div>
        </div> --}}

        @include('forms.search-form') 

        {{view('service.components.faq',compact('faqs'))}}

        @include('components.cats-product') 


       
    </div>
</section>

{{-- 
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
</script> --}}

@include('footer.footer')
