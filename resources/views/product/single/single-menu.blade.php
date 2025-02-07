@include('header.header')
{{view('components.shema-org.product',compact('product'))}}

<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <h1 class="index_title"> {{$title_h1}}</h1>    
        </div>
        <img class='img_light_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        <img class='img_black_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1_black.svg')}}" alt="">        
    </div>
</section>



<section class="product_single">
    <div class="container">

        <div class="navigation">
            <a href="/">Главная</a>/<a href="{{ route('marketplace') }}">Маркетплейс</a>/
            @if(isset($category))
                @if($category!=null)
                    <a href="{{ route('marketplace.category',$category->slug) }}">{{ $category->title }}</a>/
                @endif
            @endif
            <span>{{ $product->title }}</span>
        </div>


        <div class="grid_two_page">
            <div class="block_one_single_product">
                @if (isset($images))
                    @if (count($images)>0)
                        <div class="swiper product_swiper">
                            <div class="swiper-button-next swiper_button_next_rewies"><img src='{{asset('storage/uploads/Переключатель (4).svg')}}'></div>
                            <div class="swiper-button-prev swiper_button_prev_rewies"><img src='{{asset('storage/uploads/Переключатель (3).svg')}}'></div>
                            <div class="swiper-wrapper">
                                @foreach ($images as $image)
                                    <div class="swiper-slide">
                                        <img src="{{ $image->url() }}" alt="">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                     @endif
                @endif
                @if(count($memorial_menu)>0)
                    <div class="content_product">
                       <div class="title_news">Поминки</div>
                       <div class="text_li">Финксированное меню</div>
                       <div class="ul_memorial_menu">
                        @foreach($memorial_menu as $memorial_menu_item)
                            <div class="li_memorial_menu">
                                <div class="item_memorial_menu item_memorial_menu_1">
                                    {{$memorial_menu_item->title}}
                                </div>
                                <div class="line_gray_menu"></div>
                                <div class="item_memorial_menu">
                                    {{$memorial_menu_item->content}}
                                </div>
                            </div>
                        @endforeach
                       </div>
                    </div>
                @endif

            @if($product->content!=null)
                <div class="content_product">
                    {!! $product->content !!}
                </div>
            @endif
            
            {{view('product.components.single.reviews',compact('comments','product'))}}
                
            </div>
            <form method='post' action='{{ route('order.product.add.details') }}'class="sidebar">
                @csrf
                <input type="hidden" name="id_product" value='{{ $product->id }}'>
                <div class="cats_news product_sidebar_block">
                    <div class="title_news">
                        {{ $product->price }} ₽ <span class="gray_mini_text"> (цена без скидки)</span>
                    </div>
                    @if($product->price_sale!=null)
                        <div class="title_news">
                            {{ $product->price_sale }} ₽ <span class="gray_mini_text"> (цена со скидкой)</span>
                        </div>
                    @endif
                    {{view('product.components.single.sales',compact('sales'))}}

                    <div class="strong_gray_text">
                        Партнёр: <a href='{{$organization->route()}}'class="blue_mini_text">{{$organization->title}}</a>
                    </div>
                    <div class="title_rewies">ОГРН {{$agent->ogrn}}</div>
                    <div class="flex_main_price">
                        <div class="title_middle">Стоимость</div>
                        <div class="title_middle"><span price='{{ priceProduct($product) }}'>{{ priceProduct($product) }}</span> ₽</div>
                    </div>
                    <div class="flex_main_price">
                        <div class="count_product_single">
                            <input name='count' type="number" min=1 value=1> чел.
                        </div>
                    </div>
                </div>
                
                <div class="block_input_product_menu">
                    <div class="title_news">Дата брони</div>
                    <input required  name='date' type="date">
                </div>
                <div class="block_input_product_menu">
                    <div class="title_news">Время брони</div>
                    <input  required type="time" name='time'>
                </div>
                {{view('product.components.single.additionals',compact('additionals'))}}

                {{view('product.components.single.user-inputs')}}

                
            </form>
            <div>

            </div>
        </div>


        <div class="block_single_cemetery">
            <div class="title_our_works">Поминальный зал на карте</div>
            <div id="map_cemetery_single" style="width: 100%; height: 600px"></div>

        </div>

        <div class="">
            <h2 class='title_our_works'>Похожие {{ $product->title }}</h2>
            {{view('product.components.single.category-products',compact('category_products'))}}
        </div>    </div>
</section>





<script>
    ymaps.ready(init);

function init() {
    var myMap = new ymaps.Map("map_cemetery_single", {
            center: [{{  $product->location_width}}, {{$product->location_longitude}}],
            zoom: 10
        }, {
            searchControlProvider: 'yandex#search'
        });

      myMap.geoObjects
        .add(new ymaps.Placemark([{{ $product->location_width }}, {{ $product->location_longitude }}], {
            balloonContent: '{{ $product->title }}',
            iconCaption:  '{{ $product->title }}'
        },));
}

</script>
@include('components.cats-product') 

@include('footer.footer') 