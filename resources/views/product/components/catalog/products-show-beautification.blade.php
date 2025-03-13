@if(isset($products))
        @if($products->count()>0)
        <?php $category=$products->first()->category;?>
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
                        @if($product->size!='')
                            <div class="text_li">Размер {{ explode('|',$product->size)[0] }} </div>
                        @endif
                        @if($product->material!='')
                        <div class="text_li">{{ $product->material }} </div>
                        @endif
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




