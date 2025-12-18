
@if($reviews->count()>0)
<section class='reviews_organizations'>
    <div class="container">
        <h2 class="title">Отзывы клиентов </h2>

            <div class="swiper reviews_funeral_agencies_swiper">
                <div class="swiper-wrapper">
                @foreach($reviews as $review)
                    <div class="swiper-slide">
                        <div class="li_review_organization">
                            <div class='name_organization'>
                                <div class="title_organization">{{ $review->name }}</div>
                            </div>
                            
                            <div class="content_block">
                                <div class="content_not_all">{!!custom_echo($review->content,150)!!}</div>
                                <div class="content_all">{!!$review->content!!}</div>
                            </div>
                            <div class="raiting_memorial_dinner">
                                <img src="{{asset('storage/uploads/Star 1 copy.svg')}}" alt="">{{$review->rating}}
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>
            </div>

            <div class="swiper-button-next swiper_button_next_reviews_funeral_agencies"><img src='{{asset('storage/uploads/Переключатель.svg')}}'></div>
            <div class="swiper-button-prev swiper_button_prev_reviews_funeral_agencies"><img src='{{asset('storage/uploads/Переключатель (1) copy.svg')}}'></div>
    </div>
</section>
@endif
