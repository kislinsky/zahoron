@include('header.header')

<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <h1 class="index_title">{!! $title_h1 !!}</h1>    
        </div>
        <img class='img_light_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        <img class='img_black_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1_black.svg')}}" alt="">        
    </div>
</section>



<section class="price_service">
    <div class="container text_news_block">
        <div class="text_block">Мы гарантируем надежность и ответственность в каждом аспекте наших услуг. Наша цель - сделать процесс ухода за могилами максимально комфортным и беспроблемным для наших клиентов.<br><br>
            Если вам требуется профессиональная уборка могил с учетом всех деталей и особенностей, обращайтесь к нам. Мы готовы предложить индивидуальные условия сотрудничества и помощь в сохранении чистоты и ухода за памятью о ваших близких.
        </div>
    </div>
    <div class="container grid_two_page">
        <div class="">
            <div class="ul_news_page">
                    @if (isset($news))
                        @foreach ($news as $news_one )
                            <div class="li_news">
                                <img src="{{asset('storage/'. $news_one->img )}}" alt="">
                                <a href='{{ route('news.single',$news_one->slug) }}' class="title_news">{{ formatContent($news_one->title) }}</a>
                                <div class="text_li">{{ $news_one->created_at->format('d.m.Y') }}</div>
                            </div>
                        @endforeach
                    @endif
            </div>
        </div>
        <div class="sidebar">
            <div class="btn_border_blue"  data-bs-toggle="modal" data-bs-target="#beautification_form"><img src="{{asset('storage/uploads/Frame (20).svg')}}" alt="">Облагородить могилу</div>
            <div class="cats_news">
                <div class="title_news">Категории статей</div>
                <div class="ul_cats_news">
                    @if (isset($cats))
                    @if (count($cats) > 0)
                        @if (isset($id_cat))
                            @foreach ($cats as $cat)
                                @if($cat->id == $id_cat)
                                    <a href='{{ route('news.category', $cat->id) }}' class="li_cat_news active_cat">
                                        @if (!empty($cat->icon) && Storage::exists($cat->icon))
                                            <img src="{{ asset('storage/' . $cat->icon) }}" alt="{{ $cat->title }}">
                                        @else
                                            <div class="icon-placeholder">
                                                <!-- Можно добавить иконку-заглушку или оставить пустым -->
                                            </div>
                                        @endif
                                        {{ $cat->title }}
                                    </a>
                                @else
                                    <a href='{{ route('news.category', $cat->id) }}' class="li_cat_news">
                                        @if (!empty($cat->icon) && Storage::exists($cat->icon))
                                            <img src="{{ asset('storage/' . $cat->icon) }}" alt="{{ $cat->title }}">
                                        @else
                                            <div class="icon-placeholder">
                                                <!-- Можно добавить иконку-заглушку или оставить пустым -->
                                            </div>
                                        @endif
                                        {{ $cat->title }}
                                    </a>
                                @endif
                            @endforeach
                        @else
                            @foreach ($cats as $cat)
                                <a href='{{ route('news.category', $cat->id) }}' class="li_cat_news">
                                    @if (!empty($cat->icon) && Storage::exists($cat->icon))
                                        <img src="{{ asset('storage/' . $cat->icon) }}" alt="{{ $cat->title }}">
                                    @else
                                        <div class="icon-placeholder">
                                            <!-- Можно добавить иконку-заглушку или оставить пустым -->
                                        </div>
                                    @endif
                                    {{ $cat->title }}
                                </a>
                            @endforeach
                        @endif
                    @endif
                @endif
                </div>
            </div>
        </div>
    </div>
</section>
@include('forms.search-form') 

@include('footer.footer') 