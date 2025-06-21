<?php use App\Models\User;?>

@if (isset($reviews))
    @if (count($reviews)>0)
        <section class="rewies">
            <div class="container">
                <div class="flex_block">
                    <h2 class="title">Отзывы клиентов</h2>
                    <div class="btn_border_blue">
                        Оставить отзыв
                    </div>
                </div>

                <div class="swiper rewies_swiper">
                    <div class="swiper-wrapper">
                
                            @foreach ($reviews as $review )
                            <?php $user=User::find($review->user_id);?>
                                <div class="swiper-slide">
                                    <div class="li_rewies">
                                        <div class="grid_img_rewies">
                                            <div class="item_grid_rewies">
                                                <img src="{{asset('storage/uploads_service/'. $review->img_before )}}" alt="">
                                                <div class="title_rewies">До уборки</div>
                                            </div>
                                            <div class="item_grid_rewies">
                                                <img src="{{asset('storage/uploads_service/' .$review->img_after )}}" alt="">
                                                <div class="title_rewies">После уборки</div>
                                            </div>
                                        </div>
                                        <div class="content_block">{{ $review->content  }}</div>
                                        <div class="text_li">{{ $user->name  }} {{$user->surname}}</div>
                                    </div>
                                </div>
                            @endforeach
                    </div>
                </div>

                <div class="swiper-button-next swiper_button_next_rewies"><img src='{{asset('storage/uploads/Переключатель.svg')}}'></div>
                <div class="swiper-button-prev swiper_button_prev_rewies"><img src='{{asset('storage/uploads/Переключатель (1).svg')}}'></div>
            </div>
        </section>
    @endif
@endif