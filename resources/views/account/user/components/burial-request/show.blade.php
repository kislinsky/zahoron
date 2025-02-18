@foreach ($search_burials as $search_burial)
    <div class="li_order">
        <div class="title_li decoration_on">{{ $search_burial->surname }} {{ $search_burial->name }} {{ $search_burial->patronymic }}</div>
        <div class="mini_flex_li_product">
            <div class="title_label">Даты захоронения:</div>
            <div class="text_li">{{ $search_burial->date_birth }} - {{ $search_burial->date_death }}</div>
        </div>
        <div class="mini_flex_li_product">
            <div class="title_label">Место захоронения:</div>
            <div class="text_li">{{ $search_burial->location }}</div>
        </div>
        
        @if($search_burial->status==0)
            <div class="light_blue_btn">В работе</div>
        @elseif ($search_burial->status==1)
            <div class="red_btn reason_failure_btn">
                <div class='open_reason_failure_btn'><span>Отказ</span> <img src="{{asset('storage/uploads/Vector_red.svg')}}" alt=""></div>
                <div class="text_black">{{$search_burial->reason_failure}}</div>
            </div>
        @elseif ($search_burial->status==2)
       
            <div class="block_services_order">
                <div>
                    <div class="title_label">Фото <img src="{{ asset('storage/uploads/Vector 9.svg') }}" alt=""></div>
                    <div class="ul_services_order">
                        @if($search_burial->imgs!=null)
                            <div class="ul_imgs_order">
                                <?php $imgs=json_decode($search_burial->imgs); ?>
                                @foreach ($imgs as $img)
                                    <img src="{{ asset('storage/uploads_order/'.$img) }}" alt="">
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="flex_status_order">
                    <div class="title_label flex_direction_column">Статус заказа: 
                        @if($search_burial->paid==0)
                            <div class="text_gray">Не оплачен</div>
                        @else
                            <div class="text_black">Оплачен</div>
                        @endif
                    </div>
                    <div class="title_label flex_direction_column">Сумма: 

                        <div class="text_black">{{$search_burial->price}} ₽</div>
                    </div>


                </div>
                
                <div class="text_black_bold text_align_start">Дата заказа: <span class="text_gray">{{$search_burial->created_at->format('d.m.Y')}}</span></div>

                <div class="green_btn">Исполнено</div>

            </div>
            
        @endif


        @if($search_burial->status==2 && $search_burial->paid==0)
        <form action="{{ route('account.user.burial-request.pay',$search_burial) }}" method="get">
            @csrf
            <button style='width:100%'class="blue_btn">Оплатить</button>        
        </form>
        @endif

        <form class='accept_order text_center margin_top_auto' action="{{route('account.user.burial-request.delete',$search_burial->id)}}" method="post">
            @csrf
            @method('DELETE')
            <button class="delete_cart"><img src="{{asset('storage/uploads/Trash.svg')}}" alt=""> Отменить</button>
        </form>


        
    </div>
@endforeach