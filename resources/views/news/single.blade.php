@include('header.header')

<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <h1 class="index_title"> {{ $title_h1 }}</h1>    
            <div class="text_li">{{ $news->created_at->format('d.m.Y') }}</div>
 
        </div>
        <img class='img_light_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        <img class='img_black_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1_black.svg')}}" alt="">        
    </div>
</section>



<section class="price_service">
    <div class="container grid_two_page">
        <div class="content_news_single">
            <img class='img_main_single_news'src="{{asset('storage/'. $news->img )}}" alt="">
            {!!$news->content!!}
        </div>
        <div class="sidebar">
            <div class="btn_border_blue"  data-bs-toggle="modal" data-bs-target="#beautification_form"><img src="{{asset('storage/uploads/Frame (20).svg')}}" alt="">Облагородить могилу</div>
            <div class="cats_news">
                <div class="title_news">Категории статей</div>
                <div class="ul_cats_news">
                    @if (isset($cats))
                        @if (count($cats)>0)
                            @foreach ($cats as $cat )
                                <a href='{{ route('news.category',$cat->id) }}'class="li_cat_news"><img src="{{asset('storage/uploads_cats_news/'. $cat->icon )}}" alt="{{ $cat->title }}"> {{ $cat->title }}</a>
                            @endforeach
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@include('forms.search-form') 

@include('footer.footer') 