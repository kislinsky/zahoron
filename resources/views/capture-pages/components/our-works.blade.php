
@if (isset($our_works) && $our_works->count()>0)

    <section class="our_works_slider">
        <div class="container">
            <div class="flex_block">
                <h2 class="title">Фото работ</h2>
            </div>

            <div class="swiper our_works_swiper">
                <div class="swiper-wrapper">
                    @foreach ($our_works as $our_work )
                        <div class="swiper-slide">
                            <div class="li_our_work only_after_img">
                                <img src="{{asset('storage/'.$our_work->img_after) }}" alt="">
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-button-next swiper_button_next_our_works"><img src='{{asset('storage/uploads/Переключатель.svg')}}'></div>
            <div class="swiper-button-prev swiper_button_prev_our_works"><img src='{{asset('storage/uploads/Переключатель (1) copy.svg')}}'></div>
            </div>


        </div>
    </section>

@endif
