<ul>
    @foreach ($pages as $page)
        <li>
            @if(isset($page[1]))
            <a class='open_children_pages' >{{$page[0][0]}} <img class='img_light_theme' src='{{asset('storage/uploads/Vector 9 (1).svg')}}'>
                <img class='img_black_theme' src='{{asset('storage/uploads/Vector 9_black.svg')}}'></a>
                <ul class='children_pages_mobile_header'>
                    @foreach($page[1] as $children_page)
                        <li><a href="{{$children_page[1]}}">{{$children_page[0]}}</a></li>
                    @endforeach
                </ul>
            @else
                <a class='open_children_pages' href="{{$page[0][1]}}">{{$page[0][0]}} </a>

            @endif

        </li>
        
    @endforeach
</ul>