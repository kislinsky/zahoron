<div class="sidebar_account">

    <div class="item_page_sidebar">
        <a href='{{route('home')}}'class="title_page_sidebar"><img class='icon_page'src="{{asset('storage/uploads/Icon_sidebar_2.svg')}}" alt=""> Главная </a>
    </div>

    @foreach(userPages() as $children_pages)
        @if(isset($children_pages[2]))
            <div class="item_page_sidebar">
                <div class="title_page_sidebar"><img class='icon_page'src="{{asset($children_pages[1])}}" alt=""> {{$children_pages[0]}} <img class='open_children_pages_sidebar img_light_theme'src="{{asset('storage/uploads/Arrow_sidebar.svg')}}" alt=""><img class='open_children_pages_sidebar img_black_theme'src="{{asset('storage/uploads/Arrow_right_black.svg')}}" alt=""></div>
                <div class="pages_children_sidebar">
                    @foreach($children_pages[2] as $children_page)
                        <a  href="<?php if(isset($children_page[2])){ echo route($children_page[1],$children_page[2]);}else{ echo route($children_page[1]);}?>" class="li_children_page_sidebar {{activateLink($children_page[1], "li_children_page_sidebar_active")}}">{{$children_page[0]}}</a>
                    @endforeach
                </div>
            </div>
        @endif
        
    @endforeach

</div>
