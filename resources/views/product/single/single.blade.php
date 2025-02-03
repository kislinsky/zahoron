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
                <div class="content_product">
                    {!! $product->content !!}
                </div>
                {{view('product.components.single.reviews',compact('comments','product'))}}

                
            </div>
            <form method='post' action='{{ route('order.product.add.details') }}'class="sidebar">
                @csrf
                <input type="hidden" name="category_id" value='{{ $product->category_id }}'>
                <input type="hidden" name="product_id" value='{{ $product->id }}'>
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
                    <div style='display:none;' class="count_product_single"> <input name='count' type="hidden" min=1 value=1>  </div>
                    <div class="flex_main_price">
                        <div class="title_middle"><span price='{{ priceProduct($product) }}'>{{ priceProduct($product) }}</span> ₽</div>
                    </div>
                </div>
                <div class="cats_news product_sidebar_block">
                    <div class="title_news">Выберите размер</div>
                    <select name="size" id="">
                        @if (count($size)>0)
                            @foreach ($size as $one_size)
                                <option value="{{ $one_size }}">{{ $one_size }}</option>
                            @endforeach                            
                        @endif
                    </select>
                </div>
                {{view('product.components.single.additionals',compact('additionals'))}}

                {{view('product.components.single.user-inputs')}}

                @if(count($parameters)>0)
                <div class="cats_news product_sidebar_block">
                    <div class="title_news">Параметры {{$product->title}}</div>
                    <div class="ul_parameters">
                        @foreach ($parameters as $parameter)
                            <div class='li_parametr'><img src="{{ asset('storage/uploads/Line 1 (1).svg') }}" alt=""><div class="title_parametr">{{$parameter->title  }}:</div> <div class="black_text">{{ $parameter->content }}</div></div>
                        @endforeach
                    </div>
                </div>
                @endif
            </form>
            <div>

            </div>
        </div>

        <div class="">
            <h2 class='title_our_works'>Похожие {{ $product->title }}</h2>
            {{view('product.components.single.category-products',compact('category_products'))}}
        </div>
    </div>
</section>


<script>

</script>


@include('components.cats-product') 

@include('footer.footer') 