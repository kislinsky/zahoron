@include('header.header')

<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <div class="index_title">{{ $news->title }}</div>   
            <div class="text_li">{{ $news->created_at }}</div>
 
        </div>
        <img class='rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        
    </div>
</section>



<section class="price_service">
    <div class="container grid_two_page">
        <div class="content_news_single">
            <img class='img_main_single_news'src="{{asset('storage/uploads_news/'. $news->img )}}" alt="">
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