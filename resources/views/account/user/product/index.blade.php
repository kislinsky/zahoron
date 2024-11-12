@include('header.header-account')
<?php 
    use App\Models\ImageProduct;
    use App\Models\Product;
    use App\Models\AdditionProduct;

?>
<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <div class="index_title">Заказы с маркетплейса</div>    
        </div>
        <img class='rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        
    </div>
</section>

<section class="orders">
    <div class="container">
        <div class="flex_titles_account">
            @if (isset($status))
                <a href='{{ route('account.user.products.status',0) }}'class="btn_bac_gray <?php if($status==0){echo ' active_label_product';}?>">Новые</a>
                <a href='{{ route('account.user.products.status',1) }}'class="btn_bac_gray <?php if($status==1){echo ' active_label_product';}?>">Обработанные</a>
            @else
                <a href='{{ route('account.user.products.status',0) }}'class="btn_bac_gray">Новые</a>
                <a href='{{ route('account.user.products.status',1) }}'class="btn_bac_gray">Обработанные</a>
            @endif 
        </div>
        <div class="block_filter_cemeteries">
            <div class="text_block">Кладбища</div>
            <select name="cemetery_id" id="">
                <option value="Кладбища">Кладбища</option>
                @if(count($cemeteries)>0)
                    @foreach ($cemeteries as $cemetery)
                        <option value="{{ $cemetery->id }}">{{ $cemetery->title }}</option>
                    @endforeach
                @endif
            </select>
        </div>
        <div class="ul_orders"> 
        @if(isset($orders_products))
            @if(count($orders_products)>0)
                @foreach ($orders_products as $orders_product)
                    <div id_cemetery="{{ $orders_product->cemetery_id }}"class="li_order">
                        <?php 
                            $product=Product::findOrFail($orders_product->product_id);
                            $images=ImageProduct::where('product_id',$product->id)->get();?>
                        @if (isset($images))
                            @if (count($images)>0)
                                <img class='order_prduct_img' src="{{ asset('storage/uploads_product/'.$images[0]->title) }}" alt="">
                            @endif
                        @endif
                        <div class="mini_flex_li_product">
                            <a href='{{ route('product.single',$product->id) }}'class="title_product_market">{{ $product->title }}</a>

                            <div class="text_li color_black">Размер {{ $orders_product->size }} </div>
                            <div class="text_li color_black">{{ $product->material }} </div>
                        </div>
                       
                        @if (count(json_decode($orders_product->aplication))>0)
                            <?php $additionals=AdditionProduct::whereIn('id',json_decode($orders_product->aplication))->get();?>
                                <div class="mini_flex_li_product">
                                    <div class="title_product_market">Дополнения</div>
                                    @foreach ($additionals as $additional)
                                        <div class="text_li color_black"> {{$additional->title }} </div>
                                    @endforeach  
                                </div>
                        @endif
                        <div class="order_product_price">
                            {{ $orders_product->price*$orders_product->count }} руб.
                        </div>
                        <div class="mini_flex_li_product">
                            <div class="title_label">Дата оформления:</div>
                            <div class="text_li">{{ $orders_product->created_at }}</div>
                        </div>
                        <div class="blue_btn">Позвонить</div>
                        <a href="{{ route('account.user.products.delete',$orders_product->id) }}" class="btn_border_blue width_100">Отказаться</a>
                    </div>
                @endforeach
            @endif
        @endif
        </div>
    </div>
</section>
@include('footer.footer') 