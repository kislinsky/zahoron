<?php
use App\Models\Product;

 $city=selectCity();
 $products_memorial_dinners=Product::where('city_id', $city->id)->where('category_id',46)->get();
?>

@if(count($products_memorial_dinners)>0)

<section class="memorial_dinners">
    <div class="container">
        <div class="title">Заказать поминальные обеды в г. {{$city->title}} на маркетплейсе</div>
            <div class="swiper memorial_dinners_swiper">
                <div class="swiper-wrapper">
                @foreach($products_memorial_dinners as $product_memorial_dinner)
                    <div class="swiper-slide">
                        <div class="li_memorial_dinner">
                            <div class="flex_info_memorial_dinner">
                                <?php $organization=$product_memorial_dinner->organization;?>

                                <img src="{{$organization->urlImg()}}" alt="">
                                <div class="content_memorial_dinner">
                                    <div class="title_organization">{{$product_memorial_dinner->title_institution}}</div>
                                    <div class="raiting_memorial_dinner">
                                        <img src="{{asset('storage/uploads/Star 1 copy.svg')}}" alt="">5
                                    </div>
                                </div>
                            </div>
                            <div class="text_black">Подкатегория: Поминальные обеды</div>
                            <div class="title_memorial_dinner">{{$product_memorial_dinner->title}}</div>
                            <div class="title_memorial_dinner">{{$product_memorial_dinner->price}} ₽</div>
                            <div class="grid_btn">
                                <div id_product={{$product_memorial_dinner->id}} class="blue_btn border_radius_btn add_to_cart_product" >Оформить</div>
                                <a href='{{$product_memorial_dinner->route()}}'class="gray_btn">Подробнее</A>
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>
            </div>

            <div class="swiper-button-next swiper_button_next_memorial_dinners"><img src='{{asset('storage/uploads/Переключатель.svg')}}'></div>
            <div class="swiper-button-prev swiper_button_prev_memorial_dinners"><img src='{{asset('storage/uploads/Переключатель (1).svg')}}'></div>
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