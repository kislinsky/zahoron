
<div class="block_map_organization_single">
    <div class="block_border_gray_map_organization_single">
        <div class="flex_single_organization flex_single_organization_map">
            <div class="title_rewies">{{$cemetery->adres}}</div>
            <div class="block_btn_single_organization">
                <a href='https://yandex.ru/maps/?rtext=~{{$cemetery->width}},{{$cemetery->longitude}}' target="_target" class="icon_btn_single_organization">
                    <img class='blue_icon'src="{{asset('storage/uploads/marshryt.svg')}}" alt="">
                    <img class='white_icon'src="{{asset('storage/uploads/Vector (2).svg')}}" alt="">
                </a>
                <div class="text_black">Маршрут</div>
            </div>
        </div>
        <div class="flex_single_organization">
            <div class="text_black text_flex"><img src="{{asset('storage/uploads/mdi_clock-outline.svg')}}" alt="">  {{$cemetery->timeEndWorkingNow()}}</div>
            <img src="{{asset('storage/uploads/svg.svg')}}" alt="">
        </div>

    </div>
    <div id="map_organization_single" style="width: 100%; height: 400px"></div>
</div>

<script>
    ymaps.ready(init);

function init() {
    var myMap = new ymaps.Map("map_organization_single", {
            center: [{{  $cemetery->width}}, {{$cemetery-> longitude}}],
            zoom: 12
        }, {
            searchControlProvider: 'yandex#search'
        });

@if(count($similar_cemeteries)>0)
    @foreach($similar_cemeteries as $cemetery_one)
      myMap.geoObjects
        .add(new ymaps.Placemark(['{{$cemetery_one->width}}', '{{$cemetery_one->longitude}}'], {
            balloonContent: '{!!$cemetery_one->title.'<br> <img src="'.asset('storage/uploads/Frame 334.svg').'" alt="">  '.$cemetery_one->rating.'<br>'.$cemetery_one->countReviews().' отзывов' !!}',
            iconCaption: '{!!$cemetery_one->title!!}'
    
        },
        {
            iconLayout: 'default#image',
            iconImageHref: "{{asset('storage/uploads/mdi_grave-stone (1).svg')}}",
            iconImageSize: [40,40] // Размер иконки
        }));
    @endforeach
@endif

myMap.geoObjects
        .add(new ymaps.Placemark(['{{$cemetery->width}}', '{{$cemetery->longitude}}'], {
            balloonContent: '{!!$cemetery->title.'<br> <img src="'.asset('storage/uploads/Frame 334.svg').'" alt="">  '.$cemetery->rating.'<br>'.$cemetery->countReviews().' отзывов' !!}',
            iconCaption: '{!!$cemetery->title!!}'
    
        },
        {
            iconLayout: 'default#image',
            iconImageHref: "{{asset('storage/uploads/mdi_grave-stone (1).svg')}}",
            iconImageSize: [40,40] // Размер иконки
        }));
}


</script>