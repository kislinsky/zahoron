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
                                        <img src="{{asset('storage/uploads/Star 1 copy.svg')}}" alt="">{{ $organization->rating }}
                                    </div>
                                </div>
                            </div>
                            <div class="title_memorial_dinner">{{$product_memorial_dinner->title}}</div>
                            <div class="title_memorial_dinner">{{$product_memorial_dinner->price}} ₽</div>
                            <div class="grid_btn">
                                <a href='{{ $product_memorial_dinner->route() }}'  class="blue_btn border_radius_btn" >Оформить</a>
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

