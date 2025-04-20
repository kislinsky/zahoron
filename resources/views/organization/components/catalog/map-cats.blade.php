@if($organizations_category!=null && $organizations_category->count()>0)
    <section class="karta_all">
        <div class="container">
            <div class="title">Организации г. {{$city->title}} на карте</div>
            <div id="map-cats" style="width: 100%; height: 600px"></div>
        </div>
    </section>



    <script>
        ymaps.ready(init_organizations);
     
     function init_organizations() {
         var myMap = new ymaps.Map("map-cats", {
                 center: ['{{$city->width}}','{{$city->longitude}}'],
                 zoom: 14
             }, {
                 searchControlProvider: 'yandex#search'
             });
         @foreach($organizations_category as $organization)
         @php $organization= $organization->organization; @endphp
           myMap.geoObjects
             .add(new ymaps.Placemark(['{{$organization->width}}', '{{$organization->longitude}}'], {
                balloonContent: '{!! '<a style="color:#1A1A1A !important; text-decoration:none;" href="'.$organization->route().'">'.$organization->title.'</a><br> <img src="'.asset('storage/uploads/Frame 334.svg').'" alt="">  '.$organization->rating.'<br>'.$organization->countReviews().' отзывов' !!}',
                iconCaption: '{{$organization->title}}'
             },{
                 iconLayout: 'default#image',
                 iconImageHref: "{{asset('storage/'.$category->icon_map)}}",
                 iconImageSize: [40,40] // Размер иконки
             }));
         @endforeach
     }
     </script>
@endif