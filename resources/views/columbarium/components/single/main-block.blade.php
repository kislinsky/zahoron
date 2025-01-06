<div class="block_content_organization_single">
    <div class="main_block_organization_single">
        <div class="logo_organization_single logo_organization_single_ritual_object">
            <img src="{{$columbarium->urlImg()}}" alt="">
            <div class="blue_btn">{{$columbarium->title}}</div>
        </div>
        <div class="content_main_block_organization_single">
            <div class="flex_single_organization">
                <div class="title_li">{{$columbarium->title}}</div>
            </div>
            <div class="flex_single_organization">
                
                <div class="flex_single_organization_2">
                   
                    <div class="flex_stars">
                        @for($i=0;$i<$columbarium->rating;$i++)
                            <img src="{{asset('storage/uploads/Frame 334.svg')}}" alt="">
                        @endfor
                    </div>
                    <div class="mini_text_gray">{{count($reviews)}} отзывов</div>
                </div>
                
            </div>
            <div class="flex_info_main_single_organization margin_top_single_organization">
                <div class="text_black text_flex block_time_working">
                    <img src="{{asset('storage/uploads/mdi_clock-outline.svg')}}" alt="">{{$columbarium->timeNow()}} <img title="Открыть дни работы" class='open_working_times'src="{{asset('storage/uploads/arrow-down-svgrepo-com.svg')}}" alt="">
                    <div class="ul_working_days">
                        {!!$columbarium->ulWorkingDays()!!}
                    </div>
                </div>
                <div class="text_black">{{$columbarium->adres}}</div>
            </div>
            <div class="flex_info_main_single_organization">
                <div class="text_black">Ежедневно</div>
            </div>
            <div class="text_black margin_top_single_organization">{!!$columbarium->mini_content!!}</div>
        </div>
       
    </div>
    <div class="flex_btn_single_organization">
        <div class="block_btn_single_organization">
            <a href='tel:{{$columbarium->phone}}' class="icon_btn_single_organization">
                <img  class='blue_icon'src="{{asset('storage/uploads/Vector (1).svg')}}" alt="">
                <img  class='white_icon'src="{{asset('storage/uploads/phone_1.svg')}}" alt="">
            </a>
          <div class="text_black">Позвонить</div>
        </div>
        <div class="block_btn_single_organization">
            <a href='https://yandex.ru/maps/?rtext=~{{$columbarium->width}},{{$columbarium->longitude}}' target="_target" class="icon_btn_single_organization">
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
$('.open_or_not').html(timeWork('{{$columbarium->time_start_work}}','{{$columbarium->time_end_work}}'))

</script>