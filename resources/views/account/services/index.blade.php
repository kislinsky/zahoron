@include('header.header-account')
<?php 
    use App\Models\Burial;
    use App\Models\Service;
?>
<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <div class="index_title">Услуги</div>    
        </div>
        <img class='rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        
    </div>
</section>


<section class="orders">
    <div class="container">
        @if (isset($status))
            <div class="flex_titles_account">
                <a href='{{ route('account.services.filter',1) }}'class="btn_bac_gray <?php if($status==1){echo ' active_label_product';}?>">Оплаченные </a>
                <a href='{{ route('account.services.filter',0) }}'class="btn_bac_gray <?php if($status==0){echo ' active_label_product';}?>">Ожидают оплаты </a>
                <a href='{{ route('account.services.filter',2) }}'class="btn_bac_gray <?php if($status==2){echo ' active_label_product';}?>">В работе </a>
                <a href='{{ route('account.services.filter',3) }}'class="btn_bac_gray <?php if($status==3){echo ' active_label_product';}?>">Исполненные </a>
            </div>
        @else
            <div class="flex_titles_account">
                <a href='{{ route('account.services.filter',1) }}'class="btn_bac_gray">Оплаченные </a>
                <a href='{{ route('account.services.filter',0) }}'class="btn_bac_gray">Ожидают оплаты </a>
                <a href='{{ route('account.services.filter',2) }}'class="btn_bac_gray">В работе </a>
                <a href='{{ route('account.services.filter',3) }}'class="btn_bac_gray">Исполненные </a>
            </div>
        @endif
       
        
        <div class="ul_orders">
        @if(isset($orders))
            @if(count($orders)>0)
                @foreach ($orders as $order)
                    <?php $product=Burial::findOrFail($order->product_id);?>
                    <div class="li_order">
                        <div class="title_li decoration_on">{{ $product->surname }} {{ $product->name }} {{ $product->patronymic }}</div>
                        <div class="mini_flex_li_product">
                            <div class="title_label">Даты захоронения:</div>
                            <div class="text_li">{{ $product->date_birth }}-{{ $product->date_death }}</div>
                        </div>
                        <div class="mini_flex_li_product">
                            <div class="title_label">Место захоронения:</div>
                            <div class="text_li">{{ $product->location_death }}</div>
                        </div>
                        <a href='{{ route('burial.single',$product->id) }}'class="btn_border_blue">Подробнее</a>
                        
                        <div class="flex_status_order">
                            <div class="mini_flex_li_product">
                                <div class="title_label">Статус заказа:</div>
                                {!! statusOrder($order->status) !!}
                            </div>
                            <div class="mini_flex_li_product">
                                <div class="title_label">Сумма:</div>
                                <?php $services=Service::whereIn('id',json_decode($order->services_id))->get();?>
                                <div class="text_li color_black">{{ totalOrderService($services) }}</div>
                            </div>
                        </div>


                        <div class="mini_flex_li_product">
                            @if($order->status==0)
                                <div class="title_label data_flex">Дата заказа: <p class='text_li'>{{ $order->created_at }}</p> </div>
                            @else
                                <div class="title_label data_flex">Дата оплаты: <p class='text_li'>{{ $order->date_pay }}</p> </div>
                            @endif
                        </div>
                        @if($order->status==2)
                            <div class="light_blue_btn">В работе</div>
                        @elseif ($order->status==3)
                            <div class="green_btn">Исполнено</div>
                        @elseif ($order->status==5)
                            <div class="gray_btn">На проверке</div>
                        @endif
                        <div class="block_services_order">
                            <div class="title_label">Услуги <img src="{{ asset('storage/uploads/Vector 9.svg') }}" alt=""></div>
                            <div class="ul_services_order">
                                @if (count($services)>0)
                                    @foreach ($services as $service)
                                        <div class="title_service_order">— {{ $service->title }}</div>
                                    @endforeach
                                    
                                @endif
                                @if($order->imgs!=null)
                                    <div class="ul_imgs_order">
                                        <?php $imgs=explode('|',$order->imgs); ?>
                                        @foreach ($imgs as $img)
                                            <img src="{{ asset('storage/uploads_order/'.$img) }}" alt="">
                                        @endforeach
                                    </div>
                                @endif
                               
                            </div>
                        </div>
                        
                        @if($order->status==0)
                            <div class="blue_btn">Оплатить</div>
                        @elseif ($order->status==3)
                            <div class="blue_btn">Повторить</div>
                        @endif
                        
                    </div>
                @endforeach
           
            @endif
        @endif
        </div>
    </div>
</section>
@include('footer.footer') 