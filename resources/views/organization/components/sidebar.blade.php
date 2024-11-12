<div class="map_single_organization">
    <img src="{{asset('storage/uploads/Rectangle 1.png')}}" alt="">
    <div class="content_map_single_organization">
        <img src="{{asset('storage/uploads/el_map-marker.svg')}}" alt="">
        <a href='https://yandex.ru/maps/?rtext=~{{$organization->width}},{{$organization->longitude}}' target="_target" class="blue_btn blue_btn_mini">Объект на карте</a>
    </div>
</div>
<div class="ul_advantages_organization">
    @if($organization->available_installments!=null)
        <div class="li_advantages_organization text_black_mini"><img src="{{asset('storage/uploads/material-symbols_done.svg')}}" alt=""> Доступно в рассрочку</div>
    @endif
    @if($organization->found_cheaper!=null)
        <div class="li_advantages_organization text_black_mini"><img src="{{asset('storage/uploads/material-symbols_done.svg')}}" alt=""> Нашли дешевле снизим цену</div>
    @endif
    @if($organization->сonclusion_contract!=null)
        <div class="li_advantages_organization text_black_mini"><img src="{{asset('storage/uploads/material-symbols_done.svg')}}" alt=""> Заключение договора</div>
    @endif
    @if($organization->state_compensation!=null)
        <div class="li_advantages_organization text_black_mini"><img src="{{asset('storage/uploads/material-symbols_done.svg')}}" alt=""> Государственная компенсация</div>
    @endif
</div>