<div class="ul_product_market">
    @if(isset($products))
        @if($products->count()>0)
            @foreach ($products as $product)
                <div class="li_product_market">
                <?php $images=$product->getImages;?>
                    @if (isset($images))
                        @if (count($images)>0)
                            <img class='img_market_product' loading="lazy" src="{{ $images[0]->url() }}" alt="">
                        @endif
                    @endif
                    <a href='{{ $product->route() }}'class="title_product_market">{{ $product->title }}</a>
                    
                    <div class="flex_raiting">
                        <a href='{{$product->organization->route()}}'class="text_gray">{{$product->organization->title}}</a>
                        <div class="flex_stars">
                            <img src="{{asset('storage/uploads/Frame 334.svg')}}" alt="">
                            <div class="text_black">{{$product->organization->rating}}</div>
                        </div>
                    </div>
                    <div class="flex_btn_li_product_market">
                        <div class="price_product_market">{{ priceProduct($product) }} руб.</div>
                        {!!addToCartProduct($product->id)!!}
                    </div>
                </div>
            @endforeach
        @endif
    @endif
</div>
{{ $products->withPath(route('marketplace'))->appends($_GET)->links() }}




