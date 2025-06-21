@if($product_stocks!=null && count($product_stocks)>0)

<section class="block_content_organization_single stock_products_organization">
    <div class="flex_single_organization">
        <h2 class="title_li">Скидки на товары</h2>
    </div>
    <div class="swiper sale_products_swiper">
        <div class="swiper-wrapper">
            @foreach($product_stocks as $product_stock)
                <div class="swiper-slide">
                    <div class="li_stock">
                        <div class="text_black">Заказ более <span class='title_blue'> {{ $product_stock->price }} ₽</span></div>
                        <div class="text_gray">Скидка  <span class="text_green">{{ $product_stock->procent }} %</span></div>
                    </div>
                </div>
            @endforeach
        </div>
      </div>
</section>
@endif