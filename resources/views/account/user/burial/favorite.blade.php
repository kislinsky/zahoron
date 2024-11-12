@include('header.header-account')
<?php 
    use App\Models\Burial;
    use App\Models\Service;
?>
<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <div class="index_title">Геолокации</div>    
        </div>
        <img class='rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        
    </div>
</section>

<section class="orders">
    <div class="container">
        <div class="flex_titles_account">
            <a href='{{ route('account.burial.filter',1) }}'class="btn_bac_gray">Оплаченные</a>
            <a href='{{ route('account.burial.filter',0) }}'class="btn_bac_gray">Ожидают оплаты </a>
            <a href='{{ route('account.burial.favorite') }}'class="btn_bac_gray active_label_product"><img src='{{ asset('storage/uploads/Star 1 (3).svg') }}'>Избранное </a>
        </div>
        <div class="ul_orders">
        @if(isset($orders_products))
            @if(count($orders_products)>0)
                @foreach ($orders_products as $orders_product)
                    <?php $product=Burial::findOrFail($orders_product->burial_id);?>
                    <div class="li_order">
                        <div class="title_li decoration_on favorite_burial_title">{{ $product->surname }} {{ $product->name }} {{ $product->patronymic }} <img src='{{ asset('storage/uploads/Star 1 (2).svg') }}'></div>
                        <div class="mini_flex_li_product">
                            <div class="title_label">Даты захоронения:</div>
                            <div class="text_li">{{ $product->date_birth }}-{{ $product->date_death }}</div>
                        </div>
                        <div class="mini_flex_li_product">
                            <div class="title_label">Место захоронения:</div>
                            <div class="text_li">{{ $product->location_death }}</div>
                        </div>
                        <a href='{{ route('burial.single',$product->id) }}'class="btn_border_blue">Подробнее</a>
                        @if($orders_product->status==0)
                            <div class="blue_btn">Оплатить</div>
                            <a href='{{route('favorite.delete',$orders_product->id)}}' class="delete_cart"><img src="{{asset('storage/uploads/Trash.svg')}}" alt=""> Удалить</a>
                        @elseif($orders_product->status==1)
                            <div class="mini_flex_li_product">
                                <div class="title_label">Ссылки на карты:</div>
                                <div class="data_flex">
                                    <a target="_blank" href='https://yandex.ru/maps/?whatshere[point]={{ $product->longitude }},{{ $product->width }}&whatshere[zoom]=40'class="text_li">Яндекс Карта</a>
                                    <a target="_blank" href='https://yandex.ru/maps/?whatshere[point]={{ $product->longitude }},{{ $product->width }}&whatshere[zoom]=40'class="text_li">Яндекс Карта</a>
                                </div>
                            </div>
                            <div class="mini_flex_li_product">
                                <div class="title_label">Координаты:</div>
                                <div class="text_li">Широта: {{ $product->width }}</div>
                                <div class="text_li">Долгота:{{ $product->longitude }}</div>
                                <div adres='{{ $product->width }},{{ $product->longitude }}'class="btn_bac_gray copy_adres">Скопировать</div>
                            </div>
                        @endif
                        
                    </div>
                @endforeach
            @endif
        @endif
        </div>
    </div>
</section>
@include('footer.footer') 