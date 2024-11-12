@include('header.header')

<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <div class="index_title">{{ $cemetery->title }}</div>    
        </div>
        <img class='rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        
    </div>
</section>


<section class="cemetery">
    <div class="container">
        <div class="grid_cemetery">
            <div class="content_cemetery">
                {!! $cemetery->content !!}
                <a href='#'class="blue_btn">Позвонить</a>
            </div>
            <img src="{{asset('storage/uploads_cemeteries/'.$cemetery->img)}}" alt="">
        </div>
        <div class="block_single_cemetery">
            <div class="title_our_works">Расположение кладбища</div>
            <div class="content_cemetery">{!! $cemetery->location !!}</div>
        </div>
        <div class="block_single_cemetery">
            <div class="title_our_works">Кладбище на карте</div>
            <div id="map_cemetery_single" style="width: 100%; height: 600px"></div>

        </div>
        <div class="block_single_cemetery">
            <div class="title_our_works">Как доехать до {!! $cemetery->title !!}</div>
            <div class="content_cemetery">{!! $cemetery->how_get !!}</div>
        </div>
        <a href='https://yandex.ru/maps/?rtext=~{{ $cemetery->width }},{{ $cemetery->longitude }}' class="blue_btn"  style='max-width:150px;'>Маршрут</a>
    </div>
</section>

@include('forms.search-form') 

@include('footer.footer') 


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