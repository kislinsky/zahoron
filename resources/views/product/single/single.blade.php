@include('header.header')

<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <div class="index_title">{{ $product->title }}</div>    
        </div>
        <img class='rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        
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
                                        <img src="{{ asset('storage/uploads_product/'.$image->title) }}" alt="">
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
            <form method='get' action='{{ route('product.add.cart.details') }}'class="sidebar">
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
                    <div style='display:none;' class="count_product_single"> <input name='count' type="hidden" min=1 value=1>  </div>
                    <div class="flex_main_price">
                        <div class="title_middle"><span price='{{ priceProduct($product) }}'>{{ priceProduct($product) }}</span> ₽</div>
                        <button class="blue_btn">Купить</button>
                    </div>
                </div>
                <div class="cats_news product_sidebar_block">
                    <div class="title_news">Выберите размер памятника</div>
                    <select name="size" id="">
                        @if (count($size)>0)
                            @foreach ($size as $one_size)
                                <option value="{{ $one_size }}">{{ $one_size }}</option>
                            @endforeach                            
                        @endif
                    </select>
                </div>
                {{view('product.components.single.additionals',compact('additionals'))}}

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

        {{view('product.components.single.category-products',compact('category_products'))}}

    </div>
</section>


<script>
    $( ".add_to_cart_product" ).on( "click", function() {
    let this_btn=$(this)
    let id_product= $(this).attr('id_product');
    $.ajax({
        type: 'GET',
        url: '{{ route("product.add.cart") }}',
        data: {
            "_token": "{{ csrf_token() }}",
            'id_product': id_product,
        }, success: function (result) {
            
            if(result['error']){
                alert(result['error'])
            }else{
                this_btn.html('Купить <img src="{{asset("storage/uploads/done-v-svgrepo-com.svg")}}">')
                let price= Number($('.blue_block_all_price span').html())+Number(result['price'])
                $('.blue_block_all_price span').html(price)
                
            }
        },
        error: function () {
            alert('Ошибка');
        }
    });


    

});
</script>


@include('components.cats-product') 

@include('footer.footer') 