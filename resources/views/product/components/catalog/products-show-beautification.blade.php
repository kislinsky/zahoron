<div class="ul_product_market">
    @if(isset($products))
        @if($products->count()>0)
            @foreach ($products as $product)
                <div class="li_product_market">
                <?php $images=$product->getImages;?>
                    @if (isset($images))
                        @if (count($images)>0)
                            <img class='img_market_product' src="{{ asset('storage/uploads_product/'.$images[0]->title) }}" alt="">
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
        @endif
    @endif
</div>
{{ $products->withPath(route('marketplace'))->appends($_GET)->links() }}




<script>
    $( ".add_to_cart_product" ).on( "click", function() {
    let this_btn=$(this)
    let id_product= $(this).attr('id_product');
    $.ajax({
        type: 'GET',
        url: '{{ route("product.add.cart") }}',
        data: {
            "_token": "{{ csrf_token() }}",
            'id_product': id_product,
        }, success: function (result) {
            
            if(result['error']){
                alert(result['error'])
            }else{
                this_btn.html('Купить еще <img src="{{asset("storage/uploads/done-v-svgrepo-com.svg")}}">')
                let price= Number($('.blue_block_all_price span').html())+Number(result['price'])
                $('.blue_block_all_price span').html(price)
                
            }
        },
        error: function () {
            alert('Ошибка');
        }
    });


    

});
</script>


