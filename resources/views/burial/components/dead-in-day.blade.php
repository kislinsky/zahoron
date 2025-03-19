@if(isset($burials))
    @if($burials->count()>0)    
        <section class="burials_dead_in_day">
            <div class="container">
                <div class="ul_services">
                    <div class="title_our_works">В этот день умерли</div>
                    @foreach ($burials as $burial)
                        <div class="li_product">
                            <div class="one_block_li_product">
                                <img src="{{$burial->urlImg() }}" alt="">
                                <div class="btn_gray">{{ $burial->who }}</div>
                            </div>
                            <div class="two_block_li_product">
                                <div class="text_middle_index decoration_on">{{ $burial->surname }} {{ $burial->name }} {{ $burial->patronymic }}</div>
                                <div class="mini_flex_li_product">
                                    <div class="title_label">Даты захоронения:</div>
                                    <div class="text_li">{{ $burial->date_birth }}-{{ $burial->date_death }}</div>
                                </div>
                                <div class="mini_flex_li_burial">
                                    <div class="title_label">Место захоронения:</div>
                                    <div class="text_li">{{ $burial->location_death }}</div>
                                </div>

                                <div class="flex_btn_li_product">
                                    <a href='{{ route('burial.add',$burial->id) }}'class="blue_btn">Получить координаты</a>
                                    <a href='{{ $burial->route() }}'class="btn_border_blue">Подробнее</a>
                                    <a href='{{ route('favorite.add',$burial->id) }}'class="btn_border_blue img_mini_star"><img src="{{ asset('storage/uploads/Star 1 (1).svg')}}" alt=""></a>
                                </div>
                                
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

    @endif
@endif