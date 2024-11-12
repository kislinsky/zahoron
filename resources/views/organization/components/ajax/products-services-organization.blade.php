@if($ritual_products!=null && count($ritual_products)>0)
    <div class="ritual_products">

        @foreach($ritual_products as $ritual_product)
                <div class="li_our_product">
                    <?php $images=$ritual_product->getImages();?>
                    @if (isset($images))
                        @if (count($images)>0)
                            <img class='img_market_product' src="{{ asset('storage/uploads_product/'.$images[0]->title) }}" alt="">
                        @endif
                    @endif
                    
                    <a href='{{$ritual_product->route()}}'class="title_news">{{$ritual_product->title}} </a>
                    <?php $organization_product=$ritual_product->organization();?>
                    <div class="flex_raiting">
                        <div class="flex_stars">
                            <img src="{{asset('storage/uploads/Frame 334.svg')}}" alt="">
                            <div class="text_black_mini">{{$organization_product->rating}}</div>
                        </div>
                        <div class="text_gray_mini">{{countReviewsOrganization($organization_product)}} оценки</div>
                    
                    </div>
                    <div class="flex_btn_li_product_market">
                        <div class="title_blue">{{ priceProduct($ritual_product) }} ₽</div>
                        {!!addToCartProduct($ritual_product->id)!!}
                    </div>
                </div>
        @endforeach
    </div>
@endif

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
                this_btn.html('Купить <img src="{{asset("storage/uploads/done-v-svgrepo-com.svg")}}">')
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