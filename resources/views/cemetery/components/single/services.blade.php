@if($services!=null && count($services)>0)
    <div class="block_content_organization_single">
        <div class="title_li title_li_organization_single">Цены на услуги {{$cemetery->title}} кладбища в г. {{$city->title}}</div>
        <div class="ul_memorial_menu">
            @foreach ($services as $service)
                <div class="li_memorial_menu">
                    <div class="item_memorial_menu item_memorial_menu_1 text_black">{{$service->title}}</div>
                    <div class="line_gray_menu"></div>
                    <div class="item_memorial_menu text_blue_bold"> 
                        {{priceSerivce($service->price)}}

                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
