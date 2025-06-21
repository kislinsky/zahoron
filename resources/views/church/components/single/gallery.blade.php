
@if($images!=null && count($images)>0)

<section class="block_content_organization_single our_products_single_organization">
    <div class="flex_single_organization">
        <h2 class="title_li">Фотогалерея</h2>
    </div>
    <div class="swiper galerey_swiper">
        <div class="swiper-wrapper">
            @foreach($images as $image)
                <div class="swiper-slide">
                    <img class='img_market_product' src="{{ $image->urlImg() }}" alt="">  
                </div>
            @endforeach
        </div>
      </div>
      <div class="flex_single_organization">
            <div class="swiper-pagination swiper_pagination_galerey"></div>
            <div class="swiper_btn">
                <div class="swiper-button-prev swiper-button-prev-galerey"><img src="{{asset('storage/uploads/Vector 9_2.svg')}}" alt=""></div>
                <div class="swiper-button-next swiper-button-next-galerey"><img src="{{asset('storage/uploads/Vector 9_2.svg')}}" alt=""></div>
            </div>
      </div>
</section>
@endif