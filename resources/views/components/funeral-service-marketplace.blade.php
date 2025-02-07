<?php
use App\Models\Product;
 $city=selectCity();
 $products_funeral_service=Product::where('city_id', $city->id)->whereIn('category_id',[32,33,34,35])->get();
?>

@if(count($products_funeral_service)>0)

<section class='products_monuments_grave'>
    <div class="container">
        <div class="title">Заказать ритуальные усулги в г. {{$city->ittle}} на маркетплейсе.</div>
            <div class="swiper products_funeral_service_swiper">
                <div class="swiper-wrapper">
                @foreach($products_funeral_service as $product_funeral_service)
                    <div class="swiper-slide">
                        <div class="li_product_monuments_grave">
                            <?php $images=$product_funeral_service->getImages;?>
                            @if (isset($images))
                                @if (count($images)>0)
                                    <img class='img_market_product' src="{{ $images[0]->url() }}" alt="">
                                @endif
                            @endif
                            <a href='{{$product_funeral_service->route() }}'class="title_news">{{ $product_funeral_service->title }}</a>
                           <div class="text_li">
                            <?php $organization=$product_funeral_service->organization;?>
                            {{$organization->title}}
                           </div>
                            <div class="flex_monuments_grave">
                                {!!procentPriceProduct($product_funeral_service)!!}
                                <div class="price_product_monuments_grave">{{ priceProduct($product_funeral_service)}} руб.</div>
                                {!!addToCartProduct($product_funeral_service->id)!!}
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>
            </div>

            <div class="swiper-button-next swiper_button_next_products_funeral_service"><img src='{{asset('storage/uploads/Переключатель.svg')}}'></div>
            <div class="swiper-button-prev swiper_button_prev_products_funeral_service"><img src='{{asset('storage/uploads/Переключатель (1).svg')}}'></div>
    </div>
</section>
@endif


{{-- <script>
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
                this_btn.html('Оформить <img src="{{asset("storage/uploads/done-v-svgrepo-com.svg")}}">')
                let price= Number($('.blue_block_all_price span').html())+Number(result['price'])
                $('.blue_block_all_price span').html(price)
                
            }
        },
        error: function () {
            alert('Ошибка');
        }
    });


    

});
</script> --}}