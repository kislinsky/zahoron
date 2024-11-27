<div class="ul_organizaiotns margin_top_20">
    @if($organizations!=null && $organizations->count()>0)
        @foreach ($organizations as $organization)
            <div class="li_organization_provider">
                <div class="li_provider_flex">
                    <div class="content_li_provider">
                        <div class="text_flex">
                            <div class="green_btn">Местное время: 23:40</div>
                            {!!btnOPenOrNot($organization->openOrNot())!!}
                        </div>
                        <div class="text_black">Фирма: {{$organization->title}}</div>
                        <div class="text_black">Город: {{$organization->city()->title}}</div>
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
        {{ $organizations->withPath(route('account.agency.provider.like.organizations'))->appends($_GET)->links() }}
    @endif
</div>





