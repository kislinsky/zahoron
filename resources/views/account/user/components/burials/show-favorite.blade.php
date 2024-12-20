@foreach ($favorite_burials as $favorite_burial)
    <?php $burial=$favorite_burial->burial;?>
    <div class="li_order">
        <div class="title_li decoration_on">{{ $burial->surname }} {{ $burial->name }} {{ $burial->patronymic }}  <img src='{{ asset('storage/uploads/Star 1 (2).svg') }}'></div>
        
        <div class="mini_flex_li_product">
            <div class="title_label">Даты захоронения:</div>
            <div class="text_li">{{ $burial->date_birth }}-{{ $burial->date_death }}</div>
        </div>

        <div class="mini_flex_li_product">
            <div class="title_label">Место захоронения:</div>
            <div class="text_li">{{ $burial->location_death }}</div>
        </div>

        <a href='{{ $burial->route() }}'class="btn_border_blue">Подробнее</a>

        @if($burial->userHave())

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
            
        @else

            <div class="blue_btn">Оплатить</div>        
        @endif        
    </div>
@endforeach
