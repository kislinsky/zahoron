<div class="sidebar_account">

    <div class="item_page_sidebar">
        <a href='{{route('home')}}'class="title_page_sidebar"><img class='icon_page'src="{{asset('storage/uploads/Icon_sidebar_2.svg')}}" alt=""> Главная </a>
    </div>

    @foreach(adminPages() as $children_pages)
        <div class="item_page_sidebar">
            <div class="title_page_sidebar"><img class='icon_page'src="{{asset($children_pages[1])}}" alt=""> {{$children_pages[0]}} <img class='open_children_pages_sidebar'src="{{asset('storage/uploads/Arrow_sidebar.svg')}}" alt=""></div>
            <div class="pages_children_sidebar">
                @foreach($children_pages[2] as $children_page)
                    <a  href="{{route($children_page[1])}}" class="li_children_page_sidebar {{activateLink($children_page[1], "li_children_page_sidebar_active")}}">{{$children_page[0]}}</a>
                @endforeach
            </div>
        </div>
    @endforeach

</div>