

@if($products_sale!=null && count($products_sale)>0)

<section class="block_content_organization_single sale_products_organization">
    <div class="flex_single_organization">
        <div class="title_li">Скидки на товары</div>
    </div>
    <div class="swiper sale_products_swiper">
        <div class="swiper-wrapper">
            @foreach($products_sale as $product_sale)
                <div class="swiper-slide">
                    <div class="li_our_product">
                        <?php $images=$product_sale->images;?>
                        @if (isset($images))
                            @if (count($images)>0)
                                <img class='img_market_product' src="{{ asset('storage/uploads_product/'.$images[0]->title) }}" alt="">
                            @endif
                        @endif
                        <a href='{{$product_sale->route()}}'class="text_gray">{{$product_sale->title}} </a>
                        <div class="title_blue">{{ priceProduct($product_sale) }} ₽</div>
                        <div class="text_gray">Скидка  <span class="text_green">{{100-intdiv($product_sale->price_sale*100,$product_sale->price);}} %</span></div>

                    </div>
                </div>
            @endforeach
        </div>
      </div>
</section>
@endif