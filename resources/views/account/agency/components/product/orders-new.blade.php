<div class="orders_organization grid_mobile_1 grid_two"> 
    @if(isset($orders))
        @if(count($orders)>0)
            @foreach ($orders as $order)
                <div class="li_order">
                    <div class="box_width_border_gray">
                        <?php 
                            $product=$order->product;
                            $images=$product->getImages;?>
                        @if (isset($images))
                            @if (count($images)>0)
                                <img class='order_prduct_img' src="{{ asset('storage/uploads_product/'.$images[0]->title) }}" alt="">
                            @endif
                        @endif
                        <div class="mini_flex_li_product">
                            <a href='{{$product->route() }}'class="title_product_market">{{ $product->title }}</a>
                        </div>

                        {{view('account.agency.components.product.info-main-order',compact('order','product'))}}       

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
                            <div class="title_label">Комментарий заказчика: <span class="text_li">{{ $order->customer_comment }}</span></div>

                        </div>
                        <form class='accept_order' action="{{route('account.agency.product.order.accept',$order->id)}}" method="post">
                            @csrf
                            @method('PATCH')
                            <button class='blue_btn'>Принять</button>
                        </form>
                    </div>
                    
                   
                </div>
            @endforeach
        @endif
    @endif
</div>