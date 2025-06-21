<?php 
$city=selectCity();
$cemeteries=$city->cemeteries;
?>

<section class="karta_all">
    <div class="container">
        <h2 class="title">Морги г. {{$city->title}} на карте</h2>
        <div id="map_mortuary" style="width: 100%; height: 600px"></div>
    </div>
</section>


<script>
    ymaps.ready(init);

    function init() {
    var myMap = new ymaps.Map("map", {
            center: ['{{$city->width}}', '{{$city->longitude}}'],
            zoom: 12
        }, {
            searchControlProvider: 'yandex#search'
        });
        
@if (isset($cemeteries) && $cemeteries->count()>0)
    @foreach($cemeteries as $cemetery)
      myMap.geoObjects
        .add(new ymaps.Placemark(['{{$cemetery->width}}', '{{$cemetery->longitude}}'], {
            balloonContent: '{!!$cemetery->title.'<br> <img src="'.asset('storage/uploads/Frame 334.svg').'" alt="">  '.$cemetery->rating.'<br>'.$cemetery->countReviews().' отзывов' !!}',
            iconCaption: '{{$cemetery->title}}'
         }, {
            iconLayout: 'default#image',
            iconImageHref: "{{asset('storage/uploads/mdi_grave-stone (1).svg')}}",
            iconImageSize: [40,40] // Размер иконки
        }));
    @endforeach
@endif
}
</script>