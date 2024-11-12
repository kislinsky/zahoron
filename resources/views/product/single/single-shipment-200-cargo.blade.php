@include('header.header')

<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <div class="index_title">Заказать {{ $product->title }} в {{$organization->title}} в г. {{$city->title}}</div>    
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
                
            @if(count($parameters)>0)
                <div class="content_product text_center">
                    <div class="title_product_content">{{$product->title}}</div>
                    <div class="title_parameters"><div class="line_black_param"></div>В пакет входит<div class="line_black_param"></div></div>
                    <ul>
                        @foreach ($parameters as $parameter)
                            <li>{{$parameter->title}}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if($product->content!=null)
                <div class="content_product">
                    <div class="title_product_content">Описание {{$product->title}} от {{$organization->title}} в г. {{$city->title}}</div>
                    {!! $product->content !!}
                </div>
            @endif
            
            {{view('product.components.single.reviews',compact('comments','product'))}}
                
            </div>
            <form method='get' action='{{ route('order.product.add.details') }}'class="sidebar">
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
                    <div class="title_rewies">ОГРН {{$agent->ogrn}}</div>
                    <div class="flex_main_price">
                        <div class="title_middle">Стоимость</div>
                        <div class="title_middle"><span price='{{ priceProduct($product) }}'>{{ priceProduct($product) }}</span> ₽</div>
                    </div>
                </div>

                <div class="block_input" >
                    <div class="flex_input"><label for="">Выберите морг</label> <label class='flex_input_checkbox checkbox'><input type="checkbox" name='no_have_mortuary'>Неизвестно</label></div>
                    <div class="select">
                        <select name="mortuary_id" >
                            @if($mortuaries->count()>0)
                                @foreach($mortuaries as $mortuary)
                                    <option value="{{$mortuary->id}}">{{$mortuary->title}}</option>
                                @endforeach
                        @endif
                        </select>
                    </div>
                    @error('mortuary_id')
                        <div class='error-text'>{{ $message }}</div>
                    @enderror  
                </div> 

                <div class="block_input" >
                    <label for="">Город отправки</label>
                    <input type="text" name="city_from" id="" placeholder="Город отправки">
                    @error('city_from')
                        <div class='error-text'>{{ $message }}</div>
                    @enderror  
                </div>  

                <div class="block_input" >
                    <label for="">Город прибытия</label>
                    <input type="text" name="city_to" id="" placeholder="Город прибытия">
                    @error('city_to')
                        <div class='error-text'>{{ $message }}</div>
                    @enderror  
                </div>  

                {{view('product.components.single.additionals',compact('additionals'))}}


                {{view('product.components.single.user-inputs',compact('agent'))}}


                <button class="blue_btn">Оформить заявку</button>
            </div>  

            </form>
            <div class="block_single_cemetery">
                <div class="title_our_works">Ритуальное агенство {{$organization->title}} на карте</div>
                <div id="map_organization_single" style="width: 100%; height: 600px"></div>
    
            </div>
            </div>
           
        </div>


        

        {{view('product.components.single.category-products',compact('category_products'))}}
    </div>
</section>





<script>
    ymaps.ready(init);

function init() {
    var myMap = new ymaps.Map("map_organization_single", {
            center: [{{  $organization->width}}, {{$organization->longitude}}],
            zoom: 10
        }, {
            searchControlProvider: 'yandex#search'
        });

      myMap.geoObjects
        .add(new ymaps.Placemark([{{ $organization->width }}, {{ $organization->longitude }}], {
            balloonContent: '{{ $organization->title }}',
            iconCaption:  '{{ $organization->title }}'
        },));
}


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