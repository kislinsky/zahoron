@if(count($cities)>0)
    <div class="abs_cities">
        @foreach($cities as $city)
            <a href='{{route('city.select',$city->id)}}'class="city_li">{{$city->title}}</a>
        @endforeach
    </div>
@endif