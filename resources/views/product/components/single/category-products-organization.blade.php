@if($sameOrganizationProducts->count()>0)
    <div class="similar_products">
        <div class="swiper category_products_swiper">
            <div class="swiper-wrapper">
                @foreach($sameOrganizationProducts as $category_product)
                    <div class="swiper-slide">
                        <div class="li_product_market">
                            <?php $images = $category_product->getImages; ?>
                            @if (isset($images))
                                @if (count($images) > 0)
                                    <img class='img_market_product' src="{{ $images[0]->url() }}" alt="">
                                @endif
                            @endif
                            
                            <a href='{{ $category_product->route() }}' class="title_product_market">
                                {{ $category_product->title }}
                            </a>
                            
                            <?php $organization_product = $category_product->organization; ?>
                            <div class="text_gray_mini">{{ $organization_product->title }}</div>

                            <div class="flex_btn_li_product_market">
                                <div class="price_product_market">{{ priceProduct($category_product) }} руб.</div>
                            </div>
                            <div class="add_like_product">
                                <img src="{{ asset('storage/uploads/Group 42.svg') }}" alt="">
                            </div>
                            <div class="open_parameters">
                                <img src="{{ asset('storage/uploads/Vector (26).svg') }}" alt="">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <div class="swiper-button-next swiper_button_next_category_products">
            <img src="{{ asset('storage/uploads/Переключатель.svg') }}" alt="">
        </div>
        <div class="swiper-button-prev swiper_button_prev_category_products">
            <img src="{{ asset('storage/uploads/Переключатель (1) copy.svg') }}" alt="">
        </div>
    </div>
@endif