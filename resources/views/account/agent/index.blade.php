@include('header.header-agent')
<?php 
    use App\Models\Burial;
    use App\Models\Service;
?>
<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <div class="index_title">Здравствуйте, {{ $user->name }}! <br>Добро пожаловать в ваш личный кабинет.</div>    
        </div>
        <img class='rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        
    </div>
</section>


<section class="orders">
    <div class="container">
        <div class="title_middle">Последние заказы</div>
        <div class="ul_orders">
        @if(isset($last_orders_services))
            @if(count($last_orders_services)>0)
                @foreach ($last_orders_services as $last_orders_service)
                    <?php $product=Burial::findOrFail($last_orders_service->product_id);?>
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
                                {!! statusOrder($last_orders_service->status) !!}
                            </div>
                            <div class="mini_flex_li_product">
                                <div class="title_label">Сумма:</div>
                                <?php $services=Service::whereIn('id',json_decode($last_orders_service->services_id))->get();?>
                                <div class="text_li color_black">{{ totalOrderService($services) }}</div>
                            </div>
                        </div>


                        <div class="mini_flex_li_product">
                            @if($last_orders_service->status==0)
                                <div class="title_label data_flex">Дата заказа: <p class='text_li'>{{ $last_orders_service->created_at }}</p> </div>
                            @else
                                <div class="title_label data_flex">Дата оплаты: <p class='text_li'>{{ $last_orders_service->date_pay }}</p> </div>
                            @endif
                        </div>
                        @if($last_orders_service->status==2)
                            <div class="light_blue_btn">В работе</div>
                        @elseif ($last_orders_service->status==3)
                            <div class="green_btn">Исполнено</div>
                        @elseif ($last_orders_service->status==5)
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
                            </div>
                        </div>
                        
                        @if($last_orders_service->status==0)
                            <a href='{{ route('account.agent.service.accept',$last_orders_service->id) }}'class="blue_btn">Принять</a>
                        @endif
                        
                    </div>
                @endforeach
            @endif
        @endif
        </div>
    </div>
</section>

@include('footer.footer') 