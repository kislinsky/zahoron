
@if($organizations_category!=null && $organizations_category->count()>0)
    @foreach ($organizations_category as $organization_category)
        <?php $organization=$organization_category->organization;?>
        <div class="li_organization_provider">
            <div class="li_provider_flex">
                <div class="content_li_provider">
                    <div class="text_flex">
                        <div class="title_black_bold">Местное время: <span class='title_blue'> {{$organization->timeCity()}}</span></div>
                    </div>
                    <div class="text_black">Фирма: {{$organization->title}}</div>
                    <div class="text_black">Город: {{$organization->city->title}}</div>
                    <div class="text_black">Описание: {{$organization->mini_content}}</div>
                </div>
                <div class="content_li_provider">
                    <div class="flex_awards">
                        @if($organization->awards!=null)
                            <?php $awards=json_decode($organization->awards);?>
                            @if($awards!=null)
                                @foreach($awards as $award)
                                    <img src="{{asset('storage/uploads_organization/'.$award)}}" alt="">
                                @endforeach
                            @endif
                        @endif
                    </div>
                    <div class="title_blue">{{$organization_category->categoryProductProvider()->title}}: {{$organization_category->price}} ₽</div>
                    <div class="flex_raiting">
                        <div class="flex_stars">
                            <img src="{{asset('storage/uploads/Frame 334.svg')}}" alt="">
                            <div class="text_black">{{$organization_category->rating}}</div>
                        </div>
                        <div class="text_gray">{{countReviewsOrganization($organization)}} оценки</div>
                    
                    </div>
                </div>
            </div>
            <div class="li_provider_btn_flex">
                <a href='{{asset('storage/uploads_organization/'.$organization->remains)}}' download class="blue_btn">Остатки</a>
                <a href='{{asset('storage/uploads_organization/'.$organization->price_list)}}' download class="blue_btn">Прайс</a>
                <a href='{{$organization->route()}}' class="blue_btn">Страница</a>
                <div class="blue_btn">Чат</div>
                <a href='tel:{{$organization->phone}}' class="blue_btn">Позвонить</a>

            </div>
        </div>
    @endforeach
    {{ $organizations_category->withPath(route('organizations.provider'))->appends($_GET)->links() }}

@endif




