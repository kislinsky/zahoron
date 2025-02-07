@if(count($category_products))
    <div class="ul_products">
        @foreach($category_products as $category_product)
            <div class="li_product_market">
                <?php $images=$category_product->getImages;?>
                    @if (isset($images))
                        @if (count($images)>0)
                            <img class='img_market_product' src="{{ $images[0]->url() }}" alt="">
                        @endif
                    @endif
                    <a href='{{ $category_product->route() }}'class="title_product_market">{{ $category_product->title }}</a>
                    <?php $organization_product=$category_product->organization;?>
                    <div class="flex_raiting">
                        <div class="text_gray_mini">{{$organization_product->title}}</div>
                        <div class="flex_stars">
                            <img src="{{asset('storage/uploads/Frame 334.svg')}}" alt="">
                            <div class="text_black_mini">{{raitingOrganization($organization_product)}}</div>
                        </div>
                    </div>
                    <div class="flex_btn_li_product_market">
                        <div class="price_product_market">{{ priceProduct($category_product) }} руб.</div>
                        {!!addToCartProduct($category_product->id)!!}
                    </div>
                </div>
        @endforeach
    </div>
@endif