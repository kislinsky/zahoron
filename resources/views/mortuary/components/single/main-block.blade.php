<div class="block_content_organization_single">
    <div class="main_block_organization_single">
        <div class="logo_organization_single logo_organization_single_ritual_object">
            @if($mortuary->urlImg()=='default')
                <img class='white_img_org' src="{{$mortuary->defaultImg()[0]}}" alt="">   
                <img class='black_img_org' src="{{$mortuary->defaultImg()[1]}}" alt="">   
            @else
                <img src="{{$mortuary->urlImg()}}" alt="">   
            @endif
            <div class="blue_btn">{{$mortuary->title}}</div>
        </div>
        <div class="content_main_block_organization_single">
            <div class="flex_single_organization">
                <div class="title_li">{{$mortuary->title}}</div>
            </div>
            <div class="flex_single_organization">
                
                <div class="flex_single_organization_2">
                   
                    <div class="flex_stars">
                        @for($i=0;$i<$mortuary->rating;$i++)
                            <img src="{{asset('storage/uploads/Frame 334.svg')}}" alt="">
                        @endfor
                    </div>
                    <div class="mini_text_gray">{{count($reviews)}} отзывов</div>
                </div>
                
            </div>
            <div class="flex_info_main_single_organization margin_top_single_organization">
                <div class="text_black text_flex block_time_working">
                    <img src="{{asset('storage/uploads/mdi_clock-outline.svg')}}" alt="">{{$mortuary->timeNow()}} 
                    <img title="Открыть дни работы" class='img_light_theme open_working_times' src='{{asset('storage/uploads/Vector 9 (1).svg')}}'>
                    <img title="Открыть дни работы" class='img_black_theme open_working_times' src='{{asset('storage/uploads/Vector 9_black.svg')}}'>
                                        <div class="ul_working_days">
                        {!!$mortuary->ulWorkingDays()!!}
                    </div>
                </div>
                <div class="text_black">{{$mortuary->adres}}</div>
            </div>
            <div class="flex_info_main_single_organization">
                <div class="text_black">Ежедневно</div>
            </div>
            <div class="text_black margin_top_single_organization">{!!$mortuary->mini_content!!}</div>
        </div>
       
    </div>
    <div class="flex_btn_single_organization">
        <div class="block_btn_single_organization">
            <a href='tel:{{$mortuary->phone}}' class="icon_btn_single_organization">
                <img  class='blue_icon'src="{{asset('storage/uploads/Vector (1).svg')}}" alt="">
                <img  class='white_icon'src="{{asset('storage/uploads/phone_1.svg')}}" alt="">
            </a>
          <div class="text_black">Позвонить</div>
        </div>
        <div class="block_btn_single_organization">
            <a href='https://yandex.ru/maps/?rtext=~{{$mortuary->width}},{{$mortuary->longitude}}' target="_target" class="icon_btn_single_organization">
                <img class='blue_icon'src="{{asset('storage/uploads/marshryt.svg')}}" alt="">
                <img class='white_icon'src="{{asset('storage/uploads/Vector (2).svg')}}" alt="">
            </a>
            <div class="text_black">Маршрут</div>
        </div>
        <div class="block_btn_single_organization">
            <div class="icon_btn_single_organization open_all_reviews_organization">
                <img  class='blue_icon'src="{{asset('storage/uploads/Vector (4).svg')}}" alt="">
                <img  class='white_icon'src="{{asset('storage/uploads/Vector (6).svg')}}" alt="">
            </div>
            <div class="text_black">Отзывы</div>
        </div>
    </div>

</div>

<script>

function timeWork(time_start,time_end){
  // Получаем текущее время на устройстве пользователя
  const currentTime = new Date();

  // Получаем компоненты времени
  const hours = String(currentTime.getHours()).padStart(2, '0');
  const minutes = String(currentTime.getMinutes()).padStart(2, '0');
  const seconds = String(currentTime.getSeconds()).padStart(2, '0');
  const time_now = `${hours}:${minutes}`;

  
  if(time_now>time_start && time_now<time_end){
    return 'Окрыто';

  }
  return 'Закрыто';

}
$('.open_or_not').html(timeWork('{{$mortuary->time_start_work}}','{{$mortuary->time_end_work}}'))

</script>