@include('header.header-agent')
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


<div class="bac_black input_print_form">
    <div class='message'>
        <div class="flex_title_message">
            <div class="title_middle">Добавить фото</div>
            <div class="close_message">
                <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
            </div>
        </div>
        <form action='{{ route('account.agent.services.rent') }}' method='post' enctype='multipart/form-data' class="form_settings">
            @csrf
            <div class="block_inpit_form_search input_print">
                <input type="hidden" name="order_id" id='order_id_input' value=''>
                <div class="input__wrapper">
                    <input style='display:none;' name="file_services[]" type="file" id="input__file" multiple class="input input__file_2">
                    <label for="input__file" class="input__file-button">
                    <span class="input__file-button-text_2"><img src='{{ asset('/storage/uploads/add-icon.svg') }}'>Допускается загрузка фотографии в формате JPG и PNG размером не более 8 МБ.<br>Перетаскивайте фотографии прямо в эту область</span>
                    </label>
                </div>
                @error('file_services')
                    <div class='error-text'>{{ $message }}</div>
                @enderror
            </div>
            <button class="blue_btn btn_100">Сдать объект</button>
        </form>
    </div>
</div>



<section class="orders">
    <div class="container">
        @if (isset($status))
        <div class="flex_titles_account">
           
            <a href='{{ route('account.agent.services.filter',3) }}'class="btn_bac_gray <?php if($status==3){echo ' active_label_product';}?>">Исполненные </a>
            <a href='{{ route('account.agent.services.filter',5) }}'class="btn_bac_gray <?php if($status==5){echo ' active_label_product';}?>">На проверке </a>
            <a href='{{ route('account.agent.services.filter',2) }}'class="btn_bac_gray <?php if($status==2){echo ' active_label_product';}?>">В работе </a>
            <a href='{{ route('account.agent.services.filter',4) }}'class="btn_bac_gray <?php if($status==4){echo ' active_label_product';}?>">Новые</a>
        </div>
    @else
        <div class="flex_titles_account">
            <a href='{{ route('account.agent.services.filter',3) }}'class="btn_bac_gray">Исполненные </a>
            <a href='{{ route('account.agent.services.filter',5) }}'class="btn_bac_gray">На проверке </a>
            <a href='{{ route('account.agent.services.filter',2) }}'class="btn_bac_gray">В работе </a>
            <a href='{{ route('account.agent.services.filter',4) }}'class="btn_bac_gray">Новые </a>
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
                            <div class="title_label data_flex">Дата заказа: <p class='text_li'>{{ $order->created_at }}</p> </div>
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
                        @if ($order->worker_id==null)
                            <a href='{{ route('account.agent.service.accept',$order->id) }}'class="blue_btn">Принять</a>
                        @else
                            @if($order->status==2)
                                <div order_id='{{ $order->id }}' class="blue_btn open_rent_object">Сдать объект</div>
                            @endif
                        @endif
                        
                        
                    </div>
                @endforeach
           
            @endif
        @endif




    @if(isset($orders_2))
        @if(count($orders_2)>0)
            @foreach ($orders_2 as $order)
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
                        <div class="title_label data_flex">Дата заказа: <p class='text_li'>{{ $order->created_at }}</p> </div>

                    </div>
                    @if($order->status==2)
                        <div class="light_blue_btn">В работе</div>
                    @elseif ($order->status==3)
                        <div class="green_btn">Исполнено</div>
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
                    
                    <a href='{{ route('account.agent.service.accept',$order->id) }}'class="blue_btn">Принять</a>
                    
                </div>
            @endforeach
       
        @endif
    @endif
        </div>
    </div>
</section>
@include('footer.footer') 