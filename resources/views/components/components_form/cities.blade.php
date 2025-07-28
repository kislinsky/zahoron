@if(count($cities)>0)
    <div class="abs_cities">
        @foreach($cities as $city)
        
            <a href="{{changeUrl($city,$url)}}"  class="li_location city_li">{{ $city->title }} </a>
        @endforeach
    </div>
@endif