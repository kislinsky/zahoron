<?php 
use App\Models\ImageProduct;
?>
@if($products_our!=null && count($products_our)>0)

<section class="block_content_organization_single our_products_single_organization">
    <div class="flex_single_organization">
        <div class="title_li">Наши товары</div>
    </div>
    <div class="swiper our_products_swiper">
        <div class="swiper-wrapper">
            @foreach($products_our as $product_our)
                <div class="swiper-slide">
                    <div class="li_our_product">
                        <?php $images=ImageProduct::where('product_id',$product_our->id)->get();?>
                        @if (isset($images))
                            @if (count($images)>0)
                                <img class='img_market_product' src="{{ asset('storage/uploads_product/'.$images[0]->title) }}" alt="">
                            @endif
                        @endif
                        <a href='{{route('product.single',$product_our->id)}}'class="text_gray">{{$product_our->title}} </a>
                        <div class="title_blue">{{ priceProduct($product_our) }} руб.</div>
                    </div>
                </div>
            @endforeach
        </div>
      </div>
</section>
@endif