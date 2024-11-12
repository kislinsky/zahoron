<div class="ul_search_add_organizations">
    @foreach($organizations as $organization)
        <div class="li_search_add_organization">
            <img class='img_logo_organization'src="{{$organization->urlImg()}}" alt="">
            <div class="info_li_search_add_organization">
                <a href='{{$organization->route()}}' class="title_middle_black_bold">{{$organization->title}}</a>
                <div class="text_black">{{$organization->adres}}</div>
                <div class="blue_btn">Получить доступ</div>
            </div>
        </div>
    @endforeach
    {{ $organizations->withPath(route('account.agency.add.organization'))->appends($_GET)->links() }}

</div>