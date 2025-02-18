@foreach ($orders_burials as $order_burial)
    <?php $burial=$order_burial->burial;?>
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
        <a href='{{ $burial->route() }}'class="btn_border_blue">Подробнее</a>
        @if($order_burial->status==0)
            <form action="{{ route('account.user.burial.pay',$order_burial) }}" method="get">
                @csrf
                <button style='width:100%'class="blue_btn">Оплатить</button>        
            </form>
            <a href='{{ route('account.burial.delete',$order_burial->id) }}'class="delete_cart"><img src="{{asset('storage/uploads/Trash.svg')}}" alt=""> Отменить</a>
        @elseif($order_burial->status==1)
            <div class="mini_flex_li_product">
                <div class="title_label">Ссылки на карты:</div>
                <div class="data_flex">
                    <a target="_blank" href='https://yandex.ru/maps/?whatshere[point]={{ $burial->longitude }},{{ $burial->width }}&whatshere[zoom]=40'class="text_li">Яндекс Карта</a>
                    <a target="_blank" href='https://yandex.ru/maps/?whatshere[point]={{ $burial->longitude }},{{ $burial->width }}&whatshere[zoom]=40'class="text_li">Яндекс Карта</a>
                </div>
            </div>
            <div class="mini_flex_li_product">
                <div class="title_label">Координаты:</div>
                <div class="text_li">Широта: {{ $burial->width }}</div>
                <div class="text_li">Долгота:{{ $burial->longitude }}</div>
                <div adres='{{ $burial->width }},{{ $burial->longitude }}'class="btn_bac_gray copy_adres">Скопировать</div>
            </div>

        @endif
        
    </div>
@endforeach
