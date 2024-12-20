@foreach ($orders_services as $order_service)
    <?php $burial=$order_service->burial;?>
    <div class="li_order">
        <div class="title_li decoration_on">{{ $burial->surname }} {{ $burial->name }} {{ $burial->patronymic }}</div>
        <div class="mini_flex_li_product">
            <div class="title_label">Даты захоронения:</div>
            <div class="text_li">{{ $burial->date_birth }}-{{ $burial->date_death }}</div>
        </div>
        <div class="mini_flex_li_product">
            <div class="title_label">Место захоронения:</div>
            <div class="text_li">{{ $burial->location_death }}</div>
        </div>
        <a href='{{$burial->route() }}'class="btn_border_blue">Подробнее</a>
        
        <div class="flex_status_order">
            <div class="mini_flex_li_product">
                <div class="title_label">Статус заказа:</div>
                {!! statusOrder($order_service->status) !!}
            </div>
            <div class="mini_flex_li_product">
                <div class="title_label">Сумма:</div>
                <?php $services=$order_service->services();?>
                <div class="text_li color_black">{{ totalOrderService($services) }}</div>
            </div>
        </div>


        <div class="mini_flex_li_product">
            @if($order_service->status==0)
                <div class="title_label data_flex">Дата заказа: <p class='text_li'>{{ $order_service->created_at }}</p> </div>
            @else
                <div class="title_label data_flex">Дата оплаты: <p class='text_li'>{{ $order_service->date_pay }}</p> </div>
            @endif
        </div>


        @if($order_service->status==2)
            <div class="light_blue_btn">В работе</div>
        @elseif ($order_service->status==3)
            <div class="green_btn">Исполнено</div>
        @elseif ($order_service->status==5)
            <div class="gray_btn">На проверке</div>
        @endif

        
        <div class="block_services_order">
            <div class="title_label">Услуги <img src="{{ asset('storage/uploads/Vector 9.svg') }}" alt=""></div>
            <div class="ul_services_order">
                @if ($services->count()>0)
                    @foreach ($services as $service)
                        <div class="title_service_order">— {{ $service->title }}</div>
                    @endforeach
                    
                @endif
                @if($order_service->imgs!=null)
                    <div class="ul_imgs_order">
                        <?php $imgs=explode('|',$order_service->imgs); ?>
                        @foreach ($imgs as $img)
                            <img src="{{ asset('storage/uploads_order/'.$img) }}" alt="">
                        @endforeach
                    </div>
                @endif
                {{-- <div class="flex_imgs_order">

                </div> --}}
            </div>
        </div>
        
        @if($order_service->status==0)
            <div class="blue_btn">Оплатить</div>
        @elseif ($order_service->status==3)
            <div class="blue_btn">Повторить</div>
        @endif
        
    </div>
@endforeach
