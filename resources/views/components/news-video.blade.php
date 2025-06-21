@if (isset($news_video))
    @if (count($news_video)>0)
        <section class="news">
            <div class="container">
                <h2 class="title">Видео блог </h2>
                <div class="swiper news_video_swiper">
                    <div class="swiper-wrapper">
                        @foreach ($news_video as $news_video_one )
                            <div class="swiper-slide">
                                <div class="li_news">
                                    <a href="{{ $news_video_one->content }}" target="_target" class="href_video_news">
                                        <img src="{{asset('storage/uploads/Group 34.svg' )}}" alt="" class="open_video">
                                        <img src="{{asset('storage/'. $news_video_one->img )}}" alt="">                                
                                    </a>
                                    <div  class="title_news">{{ $news_video_one->title }}</div>
                                    <div class="text_li">{{ $news_video_one->created_at->format('d.m.Y') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="swiper-button-next swiper_button_next_video"><img src='{{asset('storage/uploads/Переключатель.svg')}}'></div>
            <div class="swiper-button-prev swiper_button_prev_video"><img src='{{asset('storage/uploads/Переключатель (1).svg')}}'></div>
            </div>
        </section>
    @endif
@endif