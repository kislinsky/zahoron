<div class="block_content_organization_single">
    <div class="main_block_organization_single">
        <div class="logo_organization_single">
            @if($organization->urlImgMain()=='default')
                <img class='white_img_org' src="{{$organization->defaultMainImg()[0]}}" alt="">   
                <img class='black_img_org' src="{{$organization->defaultMainImg()[1]}}" alt="">   
            @else
                <img src="{{$organization->urlImgMain()}}" alt="">   
            @endif
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
                        @for($i=0;$i<$organization->rating;$i++)
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
                <a href="https://yandex.ru/maps/?ll={{ $organization->width }},{{ $organization->longitude }}&pt={{ $organization->width }},{{ $organization->longitude }}&z=18"
                class="text_black"
                target="_etarget">
                    {{ $organization->adres }}
                </a>            </div>
            <div class="flex_info_main_single_organization">
                <div class="text_black">Ежедневно</div>
            </div>
            <div class="text_black margin_top_single_organization">{!!$organization->mini_content!!}</div>
        </div>
        <div class="flex_center_single_organization">
            <div class="btn_border_gray">{{$organization->openOrNot()}}</div>
            @if(versionProject())
                <div id_organization="{{ $organization->id }}" class="mini_text_blue" >Вы владелец?</div>
            @else
                @if(user()!=null)
                    <div id_organization="{{ $organization->id }}" class="mini_text_blue open_form_call_organization" >Вы владелец?</div>
                @else
                    <a href='{{ route('login') }}' class="mini_text_blue" >Вы владелец?</a>
                @endif

            @endif
        </div>
    </div>
    <div class="flex_btn_single_organization">
        <div class="block_btn_single_organization">
            
            <a href='javascript:void(0)' class="icon_btn_single_organization mgo-call-button" 
                   data-key="{{ 1 }}"
                   data-org-id="{{ $organization->id }}"
                   data-phone="{{ str_replace('+', '', $organization->phone) }}"
                   data-default-number="{{ $organization->phone }}"
                   data-calls="{{ $organization->calls }}">
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


<script>
  // Глобальная очередь для вызовов Mango Office
  window.mangoQueue = window.mangoQueue || [];
  
  // Функция для загрузки скрипта Mango Office
  function loadMangoScript(callback) {
    if (window.mgo) {
      callback(window.mgo);
      return;
    }
    
    // Добавляем callback в очередь
    window.mangoQueue.push(callback);
    
    // Если скрипт уже загружается, не инициализируем повторно
    if (window.mangoScriptLoading) return;
    window.mangoScriptLoading = true;
    
    (function(w, d, u, i, o, s, p) {
      if (d.getElementById(i)) return;
      w['MangoObject'] = o;
      w[o] = w[o] || function() { (w[o].q = w[o].q || []).push(arguments) };
      s = d.createElement('script');
      s.async = 1;
      s.id = i;
      s.src = u;
      p = d.getElementsByTagName('script')[0];
      p.parentNode.insertBefore(s, p);
      
      s.onload = function() {
        // Инициализируем Mango Office
        window.mgo({calltracking: {id: 36238}});
        
        // Выполняем все ожидающие callback'и
        while (window.mangoQueue.length) {
          let callback = window.mangoQueue.shift();
          window.mgo(function(mgo) {
            callback(mgo);
          });
        }
      };
    })(window, document, '//widgets.mango-office.ru/widgets/mango.js', 'mango-js', 'mgo');
  }

  // Обработчик для кнопок "Позвонить"
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.mgo-call-button').forEach(function(button) {
      button.addEventListener('click', function() {
        let key = this.getAttribute('data-key');
        let calls = this.getAttribute('data-calls');
        let orgId = this.getAttribute('data-org-id');
        let phone = this.getAttribute('data-phone');
        let defaultNumber = this.getAttribute('data-default-number');
        
        this.style.pointerEvents = 'none';
        



        if(calls!=0){
            // Загружаем скрипт Mango Office и получаем номер
            loadMangoScript(function(mgo) {
                mgo.getNumber({
                hash: orgId,
                redirectNumber: phone
                }, function(result) {
                if (result && result.number) {
                    let n = result.number;
                    let formattedNumber = '8 (' + n.substr(1, 3) + ') ' + n.substr(4, 3) + '-' + n.substr(7, 2) + '-' + n.substr(9, 2);
                    
                    // Обновляем кнопку
                    button.setAttribute('href', 'tel:+' + n);
                    button.style.pointerEvents = 'auto';
                    
                    // Инициируем звонок
                    window.location.href = 'tel:+' + n;
                } else {
                    
                }
                });
            });
        }else{
        $('.bac_loader').css('display','block')
        $('.load_block').css('display','block')
        setTimeout(function() {
            console.log('Недостаточно звонков')
            $('.bac_loader').css('display','none')
            $('.load_block').css('display','none')
        }, 1500);
            

        }
        
        
      });
    });
  });

  mgo({
  calltracking: {
     id: 36238,
     elements: [{selector: '.mo_phone'}],
     customParam: 'organization_id={{ $organization->id }}'
   }
});
</script>
