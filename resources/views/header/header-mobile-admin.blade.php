<div class="mobile_header">
    <div data-bs-dismiss="modal" class="close_mobile_header">
        <img src="http://127.0.0.1:8000/storage/uploads/close (2).svg" alt="">
    </div>
    <a id_city_selected='{{ $city->id }}' class="page_mobile_header city_selected">
        <img class='img_light_theme'src='{{ asset('storage/uploads/Group (22).svg') }}'>
        <img class='img_black_theme'src='{{ asset('storage/uploads/Group_black_theme.svg') }}'>{{ $city->title }}
</a>

    <?php $pages=mobilePagesAccountAdmin();?>

    {{view('header.pages',compact('pages'))}}
    
    <a class="page_mobile_header logout" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Выйти</a>

</div>