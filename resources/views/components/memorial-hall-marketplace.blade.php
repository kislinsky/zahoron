<?php
use App\Models\Product;
 $city=selectCity();
 $products_memorial_hall=Product::where('city_id', $city->id)->where('category_id',47)->get();
?>

@if(count($products_memorial_hall)>0)

<section class="memorial_dinners">
    <div class="container">
        <div class="title">Арендовать поминальный зал в г. {{$city->title}} на маркет плейсе</div>
            <div class="swiper memorial_hall_swiper">
                <div class="swiper-wrapper">
                @foreach($products_memorial_hall as $product_memorial_hall)
                    <div class="swiper-slide">
                        <div class="li_memorial_hall">
                            <?php $images=$product_memorial_hall->getImages;?>
                            @if (isset($images))
                                @if (count($images)>0)
                                    <img class='img_memorial_hall' src="{{ $images[0]->url() }}" alt="">
                                @endif
                            @endif
                            <div class="grid_two">
                                <div class="flex_info_hall">
                                    <?php $organization=$product_memorial_hall->organization;?>
                                    <img src="{{$organization->urlImg()}}" alt="">
                                    <div class="flex_hall">
                                        <a href='{{$product_memorial_hall->route()}}' class="title_memorial_hall">{{$product_memorial_hall->title}}</a>
                                        <div class="flex_monuments_grave">
                                            <div class="raiting_memorial_dinner">
                                                <img src="{{asset('storage/uploads/Star 1 copy.svg')}}" alt="">5
                                            </div>
                                            <div class="text_black">{{$product_memorial_hall->сapacity}} человек</div> 
                                        </div>
                                    </div>
                                </div>
                                <div class="flex_center">
                                    {!!addToCartProduct($product_memorial_hall->id)!!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>
            </div>

            <div class="swiper-button-next swiper_button_next_memorial_hall"><img src='{{asset('storage/uploads/Переключатель.svg')}}'></div>
            <div class="swiper-button-prev swiper_button_prev_memorial_hall"><img src='{{asset('storage/uploads/Переключатель (1).svg')}}'></div>
    </div>
</section>
@endif

