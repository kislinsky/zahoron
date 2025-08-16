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
                   data-calls="{{ $organization->haveCalls() }}">
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

  // Функция для проверки мобильного устройства
  function isMobileDevice() {
    return (typeof window.orientation !== "undefined") || (navigator.userAgent.indexOf('IEMobile') !== -1);
  }

  // Функция для копирования текста в буфер обмена
  function copyToClipboard(text) {
    const input = document.createElement('input');
    input.style.position = 'fixed';
    input.style.opacity = 0;
    input.value = text;
    document.body.appendChild(input);
    input.select();
    document.execCommand('copy');
    document.body.removeChild(input);
    
    // Показываем уведомление
    const notification = document.createElement('div');
    notification.style.position = 'fixed';
    notification.style.bottom = '20px';
    notification.style.left = '50%';
    notification.style.transform = 'translateX(-50%)';
    notification.style.backgroundColor = '#333';
    notification.style.color = '#fff';
    notification.style.padding = '10px 20px';
    notification.style.borderRadius = '5px';
    notification.style.zIndex = '10000';
    notification.textContent = 'Номер скопирован!';
    document.body.appendChild(notification);
    
    setTimeout(() => {
      document.body.removeChild(notification);
    }, 2000);
  }

  // Создаем стили для модального окна
  const style = document.createElement('style');

  document.head.appendChild(style);

  // Создаем модальное окно
  const modalOverlay = document.createElement('div');
  modalOverlay.className = 'mgo-modal-overlay';
  
  const modal = document.createElement('div');
  modal.className = 'mgo-modal';
  
  modal.innerHTML = `
    <button class="mgo-modal-close">&times;</button>
    <div class="mgo-modal-header">Позвонить</div>
    <div class="mgo-modal-note">Используется подменный номер. Не сохраняйте его - он временный.</div>
    <div class="mgo-modal-number" id="mgo-modal-number"></div>
    <button class="mgo-modal-button" id="mgo-call-button">Позвонить</button>
  `;
  
  document.body.appendChild(modalOverlay);
  document.body.appendChild(modal);

  // Обработчики закрытия модального окна
  modalOverlay.addEventListener('click', closeModal);
  modal.querySelector('.mgo-modal-close').addEventListener('click', closeModal);
  
  function closeModal() {
    modal.classList.remove('active');
    modalOverlay.style.display = 'none';
  }

  function showModal(number) {
    document.getElementById('mgo-modal-number').textContent = formatPhoneNumber(number);
    
    const callButton = document.getElementById('mgo-call-button');
    if (isMobileDevice()) {
      callButton.textContent = 'Позвонить';
      callButton.onclick = function() {
        window.location.href = 'tel:+' + number;
        closeModal();
      };
    } else {
      callButton.textContent = 'Скопировать номер';
      callButton.onclick = function() {
        copyToClipboard('+' + number);
        closeModal();
      };
    }
    
    modalOverlay.style.display = 'block';
    setTimeout(() => {
      modal.classList.add('active');
    }, 10);
  }

  // Форматирование номера телефона
  function formatPhoneNumber(number) {
    if (!number) return '';
    const n = number.toString();
    return '8 (' + n.substr(1, 3) + ') ' + n.substr(4, 3) + '-' + n.substr(7, 2) + '-' + n.substr(9, 2);
  }

  // Обработчик для кнопок "Позвонить"
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.mgo-call-button').forEach(function(button) {
      button.addEventListener('click', function() {
        let key = this.getAttribute('data-key');
        let calls = parseInt(this.getAttribute('data-calls'));
        let orgId = this.getAttribute('data-org-id');
        let phone = this.getAttribute('data-phone');
        let defaultNumber = this.getAttribute('data-default-number');
        
        // Показываем загрузку
        const originalText = this.innerHTML;
        this.style.pointerEvents = 'none';
        
        if (calls == 1) {
          // Загружаем скрипт Mango Office и получаем номер
          loadMangoScript(function(mgo) {
            mgo.getNumber({
              hash: orgId,
              redirectNumber: phone
            }, function(result) {
              if (result && result.number) {
                // Показываем модальное окно с номером
                showModal(result.number);
                
                // Восстанавливаем кнопку
                button.innerHTML = originalText;
                button.style.pointerEvents = 'auto';
              } else {
                // Если не удалось получить подменный номер, используем стандартный
                showModal(phone);
                
                // Восстанавливаем кнопку
                button.innerHTML = originalText;
                button.style.pointerEvents = 'auto';
              }
            });
          });
        } else {
          setTimeout(function() {
            button.innerHTML = originalText;
            button.style.pointerEvents = 'auto';
          showAltModal(); // Показываем окно с альтернативами вместо alert
          }, 1500);
        }
         const altStyle = document.createElement('style');
  document.head.appendChild(altStyle);

  // Создаем модальное окно для альтернативных организаций
  const altModalOverlay = document.createElement('div');
  altModalOverlay.className = 'mgo-modal-overlay';
  
  const altModal = document.createElement('div');
  altModal.className = 'mgo-alternatives-modal';
  
  // Данные альтернативных организаций (можно заменить на реальные данные)
  const alternativeOrgs = [
    @foreach ($random_organizations_with_calls as $random_organization)
      {
        id: {{ $random_organization->id }},
        name: "{{ $random_organization->name_type }}: {{ $random_organization->title }}",
        logo: "{{ $random_organization->defaultLogoImg()[0] }}",
        phone: "{{ str_replace('+', '', $random_organization->phone) }}",
        url: "{{ $random_organization->route() }}",
      },
    @endforeach
   
  ];

  function renderAlternativeOrgs() {
    let html = `
      <button class="mgo-alternatives-close">&times;</button>
      <div class="mgo-alternatives-header">У фирмы нет связи с абонентом</div>
      <div class="mgo-alternatives-note">Мы можем предложить вам проверенные ритуальные услуги</div>
    `;
    
    alternativeOrgs.forEach(org => {
      html += `
        <div class="mgo-alternative-item">
          <img src="${org.logo}" alt="${org.name}" class="mgo-alternative-logo" onerror="this.src='https://via.placeholder.com/50'">
          <div class="mgo-alternative-info">
            <div class="mgo-alternative-name">${org.name}</div>
          </div>
          <div class="mgo-alternative-actions">
            <a href="${org.url}" class="mgo-alternative-btn mgo-alternative-details">Подробнее</a>
            <button class="mgo-alternative-btn mgo-alternative-call" 
              data-phone="${org.phone}"
              data-org-id="${org.id}">Позвонить</button>
          </div>
        </div>
      `;
    });
    
    altModal.innerHTML = html;
    
    // Добавляем обработчики для кнопок "Позвонить" в альтернативных организациях
    altModal.querySelectorAll('.mgo-alternative-call').forEach(btn => {
      btn.addEventListener('click', function() {
        const phone = this.getAttribute('data-phone');
        const orgId = this.getAttribute('data-org-id');
        
        // Закрываем текущее модальное окно
        closeAltModal();
        
        // Инициируем звонок через основную систему
        initiateCall(phone, orgId);
      });
    });
    
    // Обработчик закрытия окна
    altModal.querySelector('.mgo-alternatives-close').addEventListener('click', closeAltModal);
  }

  function showAltModal() {
    renderAlternativeOrgs();
    altModalOverlay.style.display = 'block';
    setTimeout(() => {
      altModal.classList.add('active');
    }, 10);
  }

  function closeAltModal() {
    altModal.classList.remove('active');
    setTimeout(() => {
      altModalOverlay.style.display = 'none';
    }, 300);
  }

  function initiateCall(phone, orgId) {
    // Здесь можно добавить логику для инициации звонка
    // Например, открыть основное модальное окно с номером
    showModal(phone);
  }

  // Добавляем элементы в DOM
  document.body.appendChild(altModalOverlay);
  document.body.appendChild(altModal);
      });
    });
  });
</script>
