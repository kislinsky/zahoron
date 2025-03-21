@if(isset($products))
    @if($products->total()>0)
        <div class="ul_product_market">
            @foreach ($products as $product)
                <div class="li_product_market">
                <?php $images=$product->getImages;?>
                    @if (isset($images))
                        @if (count($images)>0)
                            <img class='img_market_product' loading="lazy" src="{{ $images[0]->url() }}" alt="">
                        @endif
                    @endif
                    <a href='{{ $product->route() }}'class="title_product_market">{{ $product->title }}</a>
                    
                    <div>
                        <div class="text_gray">{{$product->cafe}}</div>
                        @if($product->category_id==47)
                            <div class="text_gray">{{$product->count_people}} чел.</div>
                        @endif

                    </div>
                    <div class="flex_btn_li_product_market">
                        <div class="price_product_market">{{ priceProduct($product) }} руб.</div>
                        {!!addToCartProduct($product->id)!!}
                    </div>
                </div>
            @endforeach
        </div>
        {{ $products->withPath(route('marketplace.category',$category->slug))->appends($_GET)->links() }}
    @endif
@endif



