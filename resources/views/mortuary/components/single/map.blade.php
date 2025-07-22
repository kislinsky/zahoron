
<div class="block_map_organization_single">
    <div class="block_border_gray_map_organization_single">
        <div class="flex_single_organization flex_single_organization_map">
            <div class="title_rewies">{{$mortuary->adres}}</div>
            <div class="block_btn_single_organization">
                <a href='https://yandex.ru/maps/?rtext=~{{$mortuary->width}},{{$mortuary->longitude}}' target="_target" class="icon_btn_single_organization">
                    <img class='blue_icon'src="{{asset('storage/uploads/marshryt.svg')}}" alt="">
                    <img class='white_icon'src="{{asset('storage/uploads/Vector (2).svg')}}" alt="">
                </a>
                <div class="text_black">Маршрут</div>
            </div>
        </div>
        <div class="flex_single_organization">
            <div class="text_black text_flex"><img src="{{asset('storage/uploads/mdi_clock-outline.svg')}}" alt="">  {{$mortuary->timeEndWorkingNow()}}</div>
            <img src="{{asset('storage/uploads/svg.svg')}}" alt="">
        </div>

    </div>
    <div id="map_organization_single" style="width: 100%; height: 400px"></div>
</div>

<script>
    ymaps.ready(init);

function init() {
    var myMap = new ymaps.Map("map_organization_single", {
            center: [{{  $mortuary->width}}, {{$mortuary-> longitude}}],
            zoom: 12
        }, {
            searchControlProvider: 'yandex#search'
        });

@if(count($similar_mortuaries)>0)
    @foreach($similar_mortuaries as $mortuary_one)
      myMap.geoObjects
        .add(new ymaps.Placemark(['{{$mortuary_one->width}}', '{{$mortuary_one->longitude}}'], {
            balloonContent: '{!!$mortuary_one->title.'<br> <img src="'.asset('storage/uploads/Frame 334.svg').'" alt="">  '.$mortuary_one->rating.'<br>'.$mortuary_one->countReviews().' отзывов' !!}',
            iconCaption: '{{$mortuary_one->title}}'
        },{
            iconLayout: 'default#image',
            iconImageHref: "{{asset('storage/uploads/game-icons_morgue-feet (2).svg')}}",
            iconImageSize: [40,40] // Размер иконки
        }));
    @endforeach
@endif

  myMap.geoObjects.add(new ymaps.Placemark(['{{$mortuary->width}}', '{{$mortuary->longitude}}'], {
            balloonContent: '{!!$mortuary->title.'<br> <img src="'.asset('storage/uploads/Frame 334.svg').'" alt="">  '.$mortuary->rating.'<br>'.$mortuary->countReviews().' отзывов' !!}',
            iconCaption: '{{$mortuary->title}}'
        },{
            iconLayout: 'default#image',
            iconImageHref: "{{asset('storage/uploads/game-icons_morgue-feet (2).svg')}}",
            iconImageSize: [40,40] // Размер иконки
        }));

}



</script>