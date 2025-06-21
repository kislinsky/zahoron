<?php 
$city=selectCity();
$mortuaries=$city->mortuaries;
?>

<section class="karta_all">
    <div class="container">
        <h2 class="title">Кладбища г. {{$city->title}} на карте</h2>
        <div id="map" style="width: 100%; height: 600px"></div>
    </div>
</section>


<script>
   ymaps.ready(init_mortuary);

function init_mortuary() {
    var myMap = new ymaps.Map("map_mortuary", {
            center: ['{{$city->width}}','{{$city->longitude}}'],
            zoom: 12
        }, {
            searchControlProvider: 'yandex#search'
        });
@if(count($mortuaries)>0)
    @foreach($mortuaries as $mortuary)
      myMap.geoObjects
        .add(new ymaps.Placemark(['{{$mortuary->width}}', '{{$mortuary->longitude}}'], {
            balloonContent: '{!!$mortuary->title.'<br> <img src="'.asset('storage/uploads/Frame 334.svg').'" alt="">  '.$mortuary->rating.'<br>'.$mortuary->countReviews().' отзывов' !!}',
            iconCaption: '{{$mortuary->title}}'
        },{
            iconLayout: 'default#image',
            iconImageHref: "{{asset('storage/uploads/game-icons_morgue-feet (2).svg')}}",
            iconImageSize: [40,40] // Размер иконки
        }));
    @endforeach
@endif
}
</script>