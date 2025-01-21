<div class="sidebar_account">

    <div class="item_page_sidebar">
        <a href='{{route('home')}}'class="title_page_sidebar"><img class='icon_page'src="{{asset('storage/uploads/Icon_sidebar_2.svg')}}" alt=""> Главная </a>
    </div>

    <div class="item_page_sidebar">
        <a href='{{route('account.decoder.settings')}}'class="title_page_sidebar"><img class='icon_page'src="{{asset('storage/uploads/icon_sidebar.svg')}}" alt=""> Настройкa </a>
    </div>

    <div class="item_page_sidebar">
        <a href='{{route('account.decoder.burial.edit')}}'class="title_page_sidebar"><img class='icon_page'src="{{asset('storage/uploads/icon_sidebar_5.svg')}}" alt=""> Распознавание могил </a>
    </div>

    <div class="item_page_sidebar">
        <a href='{{route('account.decoder.settings')}}'class="title_page_sidebar"><img class='icon_page'src="{{asset('storage/uploads/icon_sidebar_6.svg')}}" alt=""> Чат </a>
    </div>


    <div class="item_page_sidebar">
        <div class="title_page_sidebar"><img class='icon_page'src="{{asset('storage/uploads/icon_sidebar_4.svg')}}" alt=""> Оплата <img class='open_children_pages_sidebar img_light_theme'src="{{asset('storage/uploads/Arrow_sidebar.svg')}}" alt=""><img class='open_children_pages_sidebar img_black_theme'src="{{asset('storage/uploads/Arrow_right_black.svg')}}" alt=""></div>
        <div class="pages_children_sidebar">
            
            @php $links = [
                ["Оплачено", 'account.decoder.payments.paid'],
                ["На проверке", 'account.decoder.payments.verification']
            ];
            @endphp

            @foreach($links as $link)
            <a  href="{{route($link[1])}}" class="li_children_page_sidebar {{activateLink($link[1], "li_children_page_sidebar_active")}}">{{$link[0]}}</a>
            @endforeach
        </div>
    </div>

    

    <div class="item_page_sidebar">
        <div class="title_page_sidebar"><img class='icon_page'src="{{asset('storage/uploads/icon_sidebar_3.svg')}}" alt=""> Обучающий
            материал <img class='open_children_pages_sidebar img_light_theme'src="{{asset('storage/uploads/Arrow_sidebar.svg')}}" alt=""><img class='open_children_pages_sidebar img_black_theme'src="{{asset('storage/uploads/Arrow_right_black.svg')}}" alt=""></div>
        <div class="pages_children_sidebar">
            @php $links = [
                ["Видео", 'account.decoder.training-material.video'],
                ["Документация", 'account.decoder.training-material.file']
            ]; @endphp
            @foreach($links as $link)
            <a  href="{{route($link[1])}}" class="li_children_page_sidebar {{activateLink($link[1], "li_children_page_sidebar_active")}}">{{$link[0]}}</a>
            @endforeach
        </div>
    </div>
    
</div>