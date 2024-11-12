
<div class="block_map_organization_single">
    <div class="block_border_gray_map_organization_single">
        <div class="flex_single_organization flex_single_organization_map">
            <div class="title_rewies">{{$columbarium->adres}}</div>
            <div class="block_btn_single_organization">
                <a href='https://yandex.ru/maps/?rtext=~{{$columbarium->width}},{{$columbarium->longitude}}' target="_target" class="icon_btn_single_organization">
                    <img class='blue_icon'src="{{asset('storage/uploads/marshryt.svg')}}" alt="">
                    <img class='white_icon'src="{{asset('storage/uploads/Vector (2).svg')}}" alt="">
                </a>
                <div class="text_black">Маршрут</div>
            </div>
        </div>
        <div class="flex_single_organization">
            <div class="text_black text_flex"><img src="{{asset('storage/uploads/mdi_clock-outline.svg')}}" alt=""> до {{$columbarium->time_end_work}}</div>
            <img src="{{asset('storage/uploads/svg.svg')}}" alt="">
        </div>

    </div>
    <div id="map_organization_single" style="width: 100%; height: 400px"></div>
</div>

<script>
    ymaps.ready(init);

function init() {
    var myMap = new ymaps.Map("map_organization_single", {
            center: [{{  $columbarium->width}}, {{$columbarium-> longitude}}],
            zoom: 12
        }, {
            searchControlProvider: 'yandex#search'
        });

@if(count($columbarium_all)>0)
    @foreach($columbarium_all as $columbarium_one)
      myMap.geoObjects
        .add(new ymaps.Placemark(['{{$columbarium_one->width}}', '{{$columbarium_one->longitude}}'], {
            balloonContent: '{!!$columbarium_one->title.'<br> <img src="'.asset('storage/uploads/Frame 334.svg').'" alt="">  '.$columbarium_one->rating.'<br>'.$columbarium_one->countReviews().' отзывов' !!}',
            iconCaption: '{!!$columbarium_one->title!!}'
        },{
            iconLayout: 'default#image',
            iconImageHref: "{{asset('storage/uploads/mdi_grave-stone (2).svg')}}",
            iconImageSize: [40,40] // Размер иконки
        }));
    @endforeach
@endif
}


</script>