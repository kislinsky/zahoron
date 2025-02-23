
@include('header.header')
<section class="order_page bac_gray">
    <div class="container order_page_search">
        <div class="content_order_page">
            <div class="index_title">Результаты поиска</div>    
            <form method='get' action="{{route('search.burial')}}" class="search">
                @csrf
                <input type="text" name='surname' placeholder='Фамилия'>
                <input type="text" name='name' placeholder='Имя'>
                <input type="text" name='patronymic' placeholder='Отчество'>
                <button class='blue_btn' type='submit'>Найти</button>
            </form>
        </div>
        <img class='img_light_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        <img class='img_black_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1_black.svg')}}" alt="">        
    </div>
</section>




<section class="price_service">
    <div class="container grid_two_page">
        <div class="">
            <div class="ul_services">
                @if(isset($products))
                    @if(count($products)>0)
                        <div class="title_our_works">Найдено {{ count($products) }} захоронения</div>
                        @foreach ($products as $product)
                            <div class="li_product">
                                <div class="one_block_li_product">
                                    <img src="{{$product->urlImg() }}" alt="">
                                    <div class="btn_gray">{{ $product->who }}</div>
                                </div>
                                <div class="two_block_li_product">
                                    <div class="text_middle_index decoration_on">{{ $product->surname }} {{ $product->name }} {{ $product->patronymic }}</div>
                                    <div class="mini_flex_li_product">
                                        <div class="title_label">Даты захоронения:</div>
                                        <div class="text_li">{{ $product->date_birth }}-{{ $product->date_death }}</div>
                                    </div>
                                    <div class="mini_flex_li_product">
                                        <div class="title_label">Место захоронения:</div>
                                        <div class="text_li">{{ $product->location_death }}</div>
                                    </div>

                                    <div class="flex_btn_li_product">
                                        <a href='{{ route('burial.add',$product->id) }}'class="blue_btn">Получить координаты</a>
                                        <a href='{{ $product->route() }}'class="btn_border_blue">Подробнее</a>
                                        <a href='{{ route('favorite.add',$product->id) }}'class="btn_border_blue img_mini_star"><img src="{{ asset('storage/uploads/Star 1 (1).svg')}}" alt=""></a>
                                    </div>
                                    
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="block_no_search">
                            <div class="title">Результаты поиска по запросу:</div>
                            <div class="text_li">По вашему запросу ничего не найдено. Проверьте корректность введённых данных или попробуйте расширить критерии поиска, например, убрав значения из некоторых полей.</div>
                            <div class="block_content_no_search bac_gray">
                                <div class="text_no_search">Если Вы не смогли найти в нашей базе интересующее Вас захоронение, Вы можете оставить заявку на его поиск</div>    
                                <a href='{{ route('page.search.burial.request') }}' class="blue_btn">Найти захоронение</a>
                            </div>    
                        </div>    
                        

                    @endif
                @endif
            </div>
        </div>
        <div class="sidebar">
            <div class="btn_border_blue"  data-bs-toggle="modal" data-bs-target="#beautification_form"><img src="{{asset('storage/uploads/Frame (20).svg')}}" alt="">Облагородить могилу</div>
            <div class="ul_news_sidebar">
                @if (isset($news))                
                    @if (isset($news))
                        @foreach ($news as $news_one )
                            <div class="li_news">
                                <img src="{{asset('storage/'. $news_one->img )}}" alt="">
                                <a href='{{ route('news.single',$news_one->id) }}' class="title_news">{{ $news_one->title }}</a>
                                <div class="text_li">{{ $news_one->created_at->format('d.m.Y') }}</div>
                            </div>
                        @endforeach                    
                    @endif
                @endif
            </div>
        </div>
    </div>
</section>
@include('footer.footer') 


