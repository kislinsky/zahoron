<div class="orders_organization grid_two grid_mobile_1"> 
    @if(isset($orders_products))
        @if(count($orders_products)>0)
            @foreach ($orders_products as $order)
                <div class="li_order">
                    <div class="box_width_border_gray">
                        <?php 
                            $product=$order->product;
                            $images=$product->getImages;?>
                        @if (isset($images))
                            @if (count($images)>0)
                                <img class='order_prduct_img' src="{{ $images[0]->url() }}" alt="">
                            @endif
                        @endif
                        <div class="mini_flex_li_product">
                            <a href='{{$product->route() }}'class="title_product_market">{{ $product->title }}</a>
                        </div>

                        {{view('account.user.components.product.info-main-order',compact('order','product'))}}       

                        @if ($order->additional!=null && count(json_decode($order->additional))>0)
                            <?php $additionals=$order->additionals();?>
                                <div class="li_order_additional">
                                    <div class="title_product_market">Дополнения</div>
                                    @foreach ($additionals as $additional)
                                        <div class="text_li color_black"> {{$additional->title }} </div>
                                    @endforeach  
                                </div>
                        @endif
                        <div class="order_product_price">
                            {{ $order->price*$order->count }} руб.
                        </div>
                    </div>
                    
                   
                    <div class="li_order_btns">
                        <div class="mini_flex_li_product">
                            <div class="title_label">Дата оформления: <span class="text_li">{{ $order->created_at->format('d.m.Y') }}</span></div>
                            <div class="title_label">Количество: <span class="text_li">{{ $order->count }}</span></div>
                        </div>
                        <a href='tel:{{$order->user->phone}}' class="blue_btn">Позвонить</a>
                        @if($order->status==0)
                            <form class='delete_order_user' action="{{route('account.user.product.delete',$order)}}" method='post'>
                                @csrf
                                @method('DELETE')
                                <button  class="btn_border_blue max_width_100">Отказаться</button>
                            </form>
                        @endif
                        

                    </div>
                </div>
            @endforeach
        @endif
    @endif
</div>