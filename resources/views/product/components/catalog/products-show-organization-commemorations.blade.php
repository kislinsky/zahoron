<div class="ul_product_market">
    @if(isset($products))
        @if($products->count()>0)
            @foreach ($products as $product)
                <div class="li_product_market">
                <?php $images=$product->getImages();?>
                    @if (isset($images))
                        @if (count($images)>0)
                            <img class='img_market_product' src="{{ asset('storage/uploads_product/'.$images[0]->title) }}" alt="">
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
        @endif
    @endif
</div>
{{ $products->withPath(route('marketplace'))->appends($_GET)->links() }}




</script>


