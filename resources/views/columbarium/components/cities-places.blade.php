@php 

$cities=selectCity()->edgeCities('columbariums');

@endphp
<section class="block_cities_places">
    <div class="container">
        <h2 class="title_middle">Города {{selectCity()->edge->title}} и их колумабрии</h2>
        @if(isset($cities) && $cities!=null && $cities->count()>0)
            <div class="ul_cities_places">
                @foreach($cities as $city_place)
                    <a href='{{$city_place->route()}}' class="li_city_place">
                        <img src="{{asset('storage/uploads/fluent_city-16-regular.svg')}}" alt=""> Колумабрии г. {{$city_place->title}}
                        <div class="line_blue_place"></div>
                    </a>
                @endforeach
            </div>

        @endif
    </div>
</section>