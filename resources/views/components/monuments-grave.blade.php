<?php
use App\Models\Product;
 $city=selectCity();
 $products_monuments_grave=Product::where('city_id', $city->id)->where('category_id',29)->get();
?>

@if(count($products_monuments_grave)>0)

<section class='products_monuments_grave'>
    <div class="container">
        <div class="title">Заказать памятники на могилу в г. {{$city->title}} на маркетплейсе.</div>
            <div class="swiper products_monuments_grave_swiper">
                <div class="swiper-wrapper">
                @foreach($products_monuments_grave as $product_monuments_grave)
                    <div class="swiper-slide">
                        <div class="li_product_monuments_grave">
                            <?php $images=$product_monuments_grave->getImages();?>
                            @if (isset($images))
                                @if (count($images)>0)
                                    <img class='img_market_product' src="{{ asset('storage/uploads_product/'.$images[0]->title) }}" alt="">
                                @endif
                            @endif
                            <a href='{{ $product_monuments_grave->route() }}'class="title_news">{{ $product_monuments_grave->title }}</a>
                            <div class="mini_text_product">
                                Размер от {{ explode('|',$product_monuments_grave->size)[0] }}. {{ $product_monuments_grave->material }}.
                            </div>
                            <div class="flex_monuments_grave">
                                {!!procentPriceProduct($product_monuments_grave)!!}
                                <div class="price_product_monuments_grave">{{ priceProduct($product_monuments_grave)}} руб.</div>
                                {!!addToCartProduct($product_monuments_grave->id)!!}
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>
            </div>

            <div class="swiper-button-next swiper_button_next_products_monuments_grave"><img src='{{asset('storage/uploads/Переключатель.svg')}}'></div>
            <div class="swiper-button-prev swiper_button_prev_products_monuments_grave"><img src='{{asset('storage/uploads/Переключатель (1).svg')}}'></div>
    </div>
</section>
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