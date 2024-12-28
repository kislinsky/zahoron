<div class="mobile_header header_mobile_agency">
    <a id_city_selected='{{ $city->id }}' class="page_mobile_header city_selected">
        <img src='{{ asset('storage/uploads/Group (22).svg') }}'>{{ $city->title }}
    </a>

    <?php $pages=mobilePagesAccountAgecny();?>

    {{view('header.pages',compact('pages'))}}
    
    <a class="page_mobile_header logout" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Выйти</a>

</div>