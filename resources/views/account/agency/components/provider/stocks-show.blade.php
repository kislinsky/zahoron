<div class="ul_organizaiotns margin_top_20">
    @if($stocks!=null && $stocks->count()>0)
        @foreach ($stocks as $stock)
        <?php $organization=$stock->organization();?>
            <div class="li_organization_provider">
                <div class="li_provider_flex">
                    <div class="content_li_provider">
                        <div class="title_middle text_decoration">Акция</div>
                        <div class="text_black"><strong>Условие:</strong> {{$stock->condition}}</div>
                        <div class="text_black">Фирма: {{$organization->title}}</div>
                        <div class="text_black">Город: {{$organization->city()->title}}</div>
                    </div>
                    <div class="content_li_provider">
                        <div class="text_flex flex_align_start">
                            <div class="green_btn text_center">Местное время: {{$organization->timeCity()}}</div>
                            {!!$stock->btnHoursUntilDate()!!}
                        </div>
                    </div>
                </div>
                <div class="li_provider_btn_flex">
                    <a href='tel:{{$organization->phone}}' class="blue_btn">Позвонить</a>
                    <a href='{{$organization->route()}}' class="gray_btn">Страница</a>
                    <a href='{{asset('storage/uploads_organization/'.$organization->remains)}}' download class="gray_btn">Остатки</a>
                    <a href='{{asset('storage/uploads_organization/'.$organization->price_list)}}' download class="gray_btn">Прайс</a>
                    <div class="gray_btn">Чат</div>

                </div>
            </div>
        @endforeach
        {{ $stocks->withPath(route('account.agency.provider.stocks'))->appends($_GET)->links() }}
    @endif
</div>





