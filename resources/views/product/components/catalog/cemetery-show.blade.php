@if ($cemetery!=null)
    
    <section class="cart_marketplace">
        <div class="container">
            <div class="title"><a href="{{ route('cemeteries.single',$cemetery->id) }}" class='text_decoration'>{{ $cemetery->title }} </a>{{ $cemetery->adres }}</div>
            <div class="block_single_cemetery">
                <div id="map_cemetery_single" style="width: 100%; height: 600px"></div>
            </div>
            <div class="text_page_marketplace">«Новое кладбище» в городе Петропавловск-Камчатский - как доехать до кладбища,
                расписание городских, рейсовых автобусов. По вопросам услуги по уходу за могилой
                вы можете задать вопрос либо позвонить по контактам указанным на странице сайт
                Мы окажем помощь в благоустройстве могилы с фотоотчетом.
            </div>
            <a href='https://yandex.ru/maps/?rtext=~{{ $cemetery->width }},{{ $cemetery->longitude }}' class="blue_btn"  style='max-width:150px;'>Маршрут</a>


        </div>
    </section>
    <script>
        ymaps.ready(init);

    function init() {
        var myMap = new ymaps.Map("map_cemetery_single", {
                center: [{{  $cemetery->width}}, {{$cemetery->longitude}}],
                zoom: 13
            }, {
                searchControlProvider: 'yandex#search'
            });

        myMap.geoObjects
        .add(new ymaps.Placemark(['{{$cemetery->width}}', '{{$cemetery->longitude}}'], {
            balloonContent: '{!!$cemetery->title.'<br> <img src="'.asset('storage/uploads/Frame 334.svg').'" alt="">  '.$cemetery->rating.'<br>'.$cemetery->countReviews().' отзывов' !!}',
            iconCaption: '{{$cemetery->title}}'
         }, {
            iconLayout: 'default#image',
            iconImageHref: "{{asset('storage/uploads/mdi_grave-stone (1).svg')}}",
            iconImageSize: [45,45] // Размер иконки
        }));
    }
    </script>
@endif
        