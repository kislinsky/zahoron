@if (isset($news))
    @if (count($news)>0)
        <section class="news">
            <div class="container">
                <div class="title">Блог</div>
                <div class="ul_news">
                    @foreach ($news as $news_one )
                        <div class="li_news">
                            <img src="{{asset('storage/uploads_news/'. $news_one->img )}}" alt="">
                            <a href='{{ route('news.single',$news_one->slug) }}' class="title_news">{{ $news_one->title }}</a>
                            <div class="text_li">{{ $news_one->created_at }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endif