@if($ritual_products!=null && count($ritual_products)>0)
    <div class="block_content_organization_single">
        <div class="title_li title_li_organization_single">Ритуальные товары</div>
        <div class="ul_memorial_menu">
            @foreach ($ritual_products as $ritual_product)
                <div class="li_memorial_menu">
                    <div class="item_memorial_menu item_memorial_menu_1 text_black">{{$ritual_product->title}}</div>
                    <div class="line_gray_menu"></div>
                    <div class="item_memorial_menu text_blue_bold"> {{priceProduct($ritual_product)}} ₽</div>
                </div>
            @endforeach
        </div>
    </div>
@endif
