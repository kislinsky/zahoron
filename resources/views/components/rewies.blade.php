<?php         
use App\Models\Review;
$reviews = Review::orderBy('id', 'desc')->get();

?>

<section class="rewies">
    <div class="container">
        <div class="flex_block">
            <div class="title">Отзывы клиентов</div>
            <div class="btn_border_blue">
                Оставить отзыв
            </div>
        </div>

        <div class="swiper rewies_swiper">
            <div class="swiper-wrapper">
            @if (isset($reviews))
                @if (count($reviews)>0)
                    @foreach ($reviews as $review )
                        <div class="swiper-slide">
                            <div class="li_rewies">
                                <div class="grid_img_rewies">
                                    <div class="item_grid_rewies">
                                        <img src="{{asset('storage/uploads_reviews/'. $review->img_before )}}" alt="">
                                        <div class="title_rewies">До уборки</div>
                                    </div>
                                    <div class="item_grid_rewies">
                                        <img src="{{asset('storage/uploads_reviews/' .$review->img_after )}}" alt="">
                                        <div class="title_rewies">После уборки</div>
                                    </div>
                                </div>
                                <div class="content_block">{{ $review->content  }}</div>
                                <div class="text_li">{{ $review->name  }}</div>
                            </div>
                        </div>
                    @endforeach
                @endif
            @endif
               
            </div>
        </div>

        <div class="swiper-button-next swiper_button_next_rewies"><img src='{{asset('storage/uploads/Переключатель.svg')}}'></div>
        <div class="swiper-button-prev swiper_button_prev_rewies"><img src='{{asset('storage/uploads/Переключатель (1).svg')}}'></div>
    </div>
</section>