<div class="block_content_organization_single">
    <div class="main_block_organization_single">
        <div class="logo_organization_single">
            <img src="{{$organization->urlImg()}}" alt="">
            <div class="blue_btn">{{$organization->title}}</div>
        </div>
        <div class="content_main_block_organization_single">
            <div class="flex_single_organization">
                <div class="title_li">{{$organization->title}}</div>
            </div>
            <h1 class="mobile_title_organization title_li">
                 {{$organization->title}} в г. {{$organization->city->title}} 
            </h1>
            <div class="text_black name_type_organization">{{$organization->name_type}}</div>

            <div class="flex_single_organization">
                
                <div class="flex_single_organization_2">
                   
                    <div class="flex_stars">
                        @for($i=0;$i<$rating_reviews;$i++)
                            <img src="{{asset('storage/uploads/Frame 334.svg')}}" alt="">
                        @endfor
                    </div>
                    <div class="mini_text_gray">{{count($reviews)}} отзывов</div>
                </div>
                
            </div>
            <div class="flex_info_main_single_organization margin_top_single_organization">
                <div class="text_black text_flex block_time_working">
                    <img src="{{asset('storage/uploads/mdi_clock-outline.svg')}}" alt="">{{$organization->timeNow()}} 
                    <img title="Открыть дни работы" class='img_light_theme open_working_times' src='{{asset('storage/uploads/Vector 9 (1).svg')}}'>
                    <img title="Открыть дни работы" class='img_black_theme open_working_times' src='{{asset('storage/uploads/Vector 9_black.svg')}}'>
                    <div class="ul_working_days">
                        {!!$organization->ulWorkingDays()!!}
                    </div>
                </div>
                <div class="text_black">{{$organization->adres}}</div>
            </div>
            <div class="flex_info_main_single_organization">
                <div class="text_black">Ежедневно</div>
            </div>
            <div class="text_black margin_top_single_organization">{!!$organization->mini_content!!}</div>
        </div>
        <div class="flex_center_single_organization">
            <div class="btn_border_gray">{{$organization->openOrNot()}}</div>
            <div class="mini_text_blue">Вы владелец?</div>
        </div>
    </div>
    <div class="flex_btn_single_organization">
        <div class="block_btn_single_organization">
            <a href='tel:{{$organization->phone}}' class="icon_btn_single_organization">
                <img  class='blue_icon'src="{{asset('storage/uploads/Vector (1).svg')}}" alt="">
                <img  class='white_icon'src="{{asset('storage/uploads/phone_1.svg')}}" alt="">
            </a>
          <div class="text_black">Позвонить</div>
        </div>
        <div class="block_btn_single_organization">
            <a href='https://yandex.ru/maps/?rtext=~{{$organization->width}},{{$organization->longitude}}' target="_target" class="icon_btn_single_organization">
                <img class='blue_icon'src="{{asset('storage/uploads/marshryt.svg')}}" alt="">
                <img class='white_icon'src="{{asset('storage/uploads/Vector (2).svg')}}" alt="">
            </a>
            <div class="text_black">Маршрут</div>
        </div>
        <div class="block_btn_single_organization">
            <a href='{{route('organization.like.add',$organization->id)}}' class="icon_btn_single_organization">
                <img  class='blue_icon'src="{{asset('storage/uploads/Vector (3).svg')}}" alt="">
                <img  class='white_icon'src="{{asset('storage/uploads/Vector (5).svg')}}" alt="">
            </a>
            <div class="text_black">Избранное</div>
        </div>
    </div>
</div>