<div class="mobile_header">
    <a id_city_selected='{{ $city->id }}' class="page_mobile_header city_selected">
        <img class='img_light_theme'src='{{ asset('storage/uploads/Group (22).svg') }}'>
                <img class='img_black_theme'src='{{ asset('storage/uploads/Group_black_theme.svg') }}'>{{ $city->title }}
    </a>

    <?php $pages=mobilePagesAccountAgent();?>

    {{view('header.pages',compact('pages'))}}
    
    <a class="page_mobile_header logout" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Выйти</a>

</div>