
@if($organizations_category!=null && $organizations_category->total()>0)

    <?php $category=$organizations_category->first()->categoryProduct;?>

    @foreach ($organizations_category as $key=>$organization_category)

        <?php $organization=$organization_category->organization;?>



        <div class="li_organization <?php if($organization->priority){echo 'priority';}?>">

            <?php $category_organiaztion=$organization_category->categoryProduct;?>


            <div class="info_li_org_mobile li_org_mobile">

                @if($organization->urlImgMain()=='default')
                    <a href='{{ $organization->route() }}' ><img class='white_img_org img_logo_organization' src="{{$organization->defaultMainImg()[0]}}" alt=""></a>
                    <a href='{{ $organization->route() }}' ><img class='black_img_org img_logo_organization' src="{{$organization->defaultMainImg()[1]}}" alt=""> </a>
                @else
                    <a href='{{ $organization->route() }}' ><img class='img_logo_organization' src="{{$organization->urlImgMain()}}" alt=""></a>
                @endif

                <div class="info_li_organization">
                  @if($organization->priority)
                    <div class="priority_flex">
                      <img src="{{ asset('storage/uploads/Vector_premium.svg') }}" alt="">
                      <div class="text_black_bold">Premium</div>
                    </div>
                  @endif
                    <a href='{{$organization->route()}}'class="title_li_organiaztion">{{$organization->title}}</a>
                    <div class="text_gray">{{$organization->name_type}}</div>
                    <div class="text_gray"> {{$organization->adres}}</div>
                </div>
            </div>

            <div class="info_li_org_mobile li_org_mobile"><div class="text_black"><img src="{{ asset('storage/uploads/Frame 334.svg') }}" alt=""> {{ $organization->rating }} - {{countReviewsOrganization($organization)}} оценок</div>   <div class="text_black">{{$organization->timeEndWorkingNow()}}</div></div>


            <div class="li_logo_organization li_org_dekstop">
                @if($organization->urlImgMain()=='default')
                     <a href='{{ $organization->route() }}' ><img class='white_img_org img_logo_organization' src="{{$organization->defaultMainImg()[0]}}" alt="">   </a>
                     <a href='{{ $organization->route() }}' ><img class='black_img_org img_logo_organization' src="{{$organization->defaultMainImg()[1]}}" alt="">  </a>
                @else
                     <a href='{{ $organization->route() }}' ><img class='img_logo_organization' src="{{$organization->urlImgMain()}}" alt="">   </a>
                @endif
                <div class="flex_stars">
                    <img src="{{asset('storage/uploads/Frame 334.svg')}}" alt=""> <div class="text_black">{{$organization->rating}}</div>
                </div>
            </div>

            <div class="info_li_organization li_org_dekstop">
                @if($organization->priority)
                    <div class="priority_flex">
                      <img src="{{ asset('storage/uploads/Vector_premium.svg') }}" alt="">
                      <div class="text_black_bold">Premium</div>
                    </div>
                  @endif
                <a href='{{$organization->route()}}'class="title_li_organiaztion"> {{$organization->title}}</a>
                <div class="text_gray">{{$organization->name_type}}</div>
                <div class="text_gray"> {{$organization->adres}}</div>
                <div class="text_black"><img src="{{ asset('storage/uploads/Frame 334.svg') }}" alt=""> {{countReviewsOrganization($organization)}} оценки - {{$organization->timeEndWorkingNow()}}</div>
            </div>

            <div class="info_li_org_mobile li_org_mobile">
                <div class='info_li_organization'>
                    <div class="text_black_bold">{{$category_organiaztion->title}}</div>
                    <div class="text_gray">Сумма</div>
                    <div class="text_black_bold"> <span class='title_blue'>{{$organization_category->priceHtml()}} </span></div>
                </div>
                <div class="li_flex_icon_organization">
                  @if($organization->getIsLikedAttribute())
                    <a href="{{route('organization.like.add',$organization->id)}}"><img src="{{asset('storage/uploads/VectorLike.svg')}}" alt=""></a>
                  @else
                    <a href="{{route('organization.like.add',$organization->id)}}"><img src="{{asset('storage/uploads/Vector_not_like.svg')}}" alt=""></a>
                  @endif
                  <div val='{{ $organization->route() }}' class='share_button'><img src="{{asset('storage/uploads/Vector (8).svg')}}" alt=""></div>
                </div>
            </div>

            <div class="li_price_category_organization li_org_dekstop">
                <div class="text_gray">{{$category_organiaztion->title}}</div>
                <div class="title_blue"> {{$organization_category->priceHtml()}} </div>
            </div>

            <div class="li_flex_btn_organization">
                @if($organization->city->edge->call_mango_office)
                  <a id='{{ $organization->id }}' href='javascript:void(0)' class="blue_btn mgo-call-button"
                    data-key="{{ $key }}"
                    data-org-id="{{ $organization->id }}"
                    data-phone="{{ str_replace('+', '', $organization->phone) }}"
                    data-default-number="{{ $organization->phone }}"
                    data-calls="{{ $organization->haveCalls() }}"
                  >
                    Позвонить
                  </a>
                @else
                  <a href="tel:{{ $organization->phone }}" class="blue_btn" >Позвонить</a>
                @endif

                <a href='{{$organization->route()}}' class="btn_border_blue">Подробнее</a>
            </div>

            <div class="li_flex_icon_organization li_org_dekstop">
              @if($organization->getIsLikedAttribute())
                <a href="{{route('organization.like.add',$organization->id)}}"><img src="{{asset('storage/uploads/VectorLike.svg')}}" alt=""></a>
              @else
                <a href="{{route('organization.like.add',$organization->id)}}"><img src="{{asset('storage/uploads/Vector_not_like.svg')}}" alt=""></a>
              @endif
                <div val='{{ $organization->route() }}' class='share_button'><img src="{{asset('storage/uploads/Vector (8).svg')}}" alt=""></div>
            </div>
        </div>



    @endforeach

    {{ $organizations_category->withPath(route('organizations.category',$category->slug))->appends($_GET)->links() }}


<script>
    document.addEventListener('DOMContentLoaded', function() {
  // Создаем элемент для уведомлений
  const notification = document.createElement('div');
  notification.id = 'shareNotification';
  notification.style.cssText = `
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: #4CAF50;
    color: white;
    padding: 12px 24px;
    border-radius: 4px;
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    display: none;
    animation: fadeIn 0.3s;
  `;
  document.body.appendChild(notification);

  // Обработчик для всех кнопок поделиться
  document.querySelectorAll('.share_button').forEach(button => {
    button.addEventListener('click', async function() {
      const link = this.getAttribute('val');
      if (!link) {
        showNotification('Ошибка: ссылка не найдена', 'error');
        return;
      }

      // Проверяем мобильное устройство и поддержку Web Share API
      const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

      if (isMobile && navigator.share) {
        // Пытаемся использовать нативный API для мобильных устройств
        try {
          await navigator.share({
            title: 'Поделиться ссылкой',
            text: 'Посмотрите эту организацию',
            url: link
          });
        } catch (err) {
          // Если пользователь отменил шаринг, не показываем ошибку
          if (err.name !== 'AbortError') {
            console.error('Ошибка при попытке поделиться:', err);
            copyToClipboard(link);
          }
        }
      } else {
        // Для десктопа или если Web Share API не поддерживается
        copyToClipboard(link);
      }
    });
  });

  // Функция копирования в буфер обмена
  function copyToClipboard(text) {
    try {
      navigator.clipboard.writeText(text).then(() => {
        showNotification('Ссылка скопирована!');
      }).catch(err => {
        console.error('Ошибка при копировании:', err);
        useFallbackCopyMethod(text);
      });
    } catch (err) {
      console.error('Ошибка при доступе к буферу обмена:', err);
      useFallbackCopyMethod(text);
    }
  }

  // Резервный метод копирования для старых браузеров
  function useFallbackCopyMethod(text) {
    try {
      const textArea = document.createElement('textarea');
      textArea.value = text;
      textArea.style.position = 'fixed';
      textArea.style.opacity = 0;
      document.body.appendChild(textArea);
      textArea.select();

      const successful = document.execCommand('copy');
      document.body.removeChild(textArea);

      if (successful) {
        showNotification('Ссылка скопирована!');
      } else {
        showNotification('Не удалось скопировать ссылку', 'error');
      }
    } catch (err) {
      console.error('Ошибка при использовании fallback метода:', err);
      showNotification('Ошибка при копировании', 'error');
    }
  }

  // Показать уведомление
  function showNotification(message, type = 'success') {
    notification.textContent = message;
    notification.style.display = 'block';
    notification.style.background = type === 'error' ? '#f44336' : '#4CAF50';

    setTimeout(() => {
      notification.style.display = 'none';
    }, 3000);
  }

  // Добавляем CSS анимацию
  const style = document.createElement('style');
  style.textContent = `
    @keyframes fadeIn {
      from { opacity: 0; transform: translateX(-50%) translateY(20px); }
      to { opacity: 1; transform: translateX(-50%) translateY(0); }
    }
  `;
  document.head.appendChild(style);
});
</script>







<script>
  // Загрузка виджета Mango Office
  (function(w, d, u, i, o, s, p) {
      if (d.getElementById(i)) { return; } w['MangoObject'] = o;
      w[o] = w[o] || function() { (w[o].q = w[o].q || []).push(arguments) }; w[o].u = u; w[o].t = 1 * new Date();
      s = d.createElement('script'); s.async = 1; s.id = i; s.src = u;
      p = d.getElementsByTagName('script')[0]; p.parentNode.insertBefore(s, p);
  }(window, document, '//widgets.mango-office.ru/widgets/mango.js', 'mango-js', 'mgo'));

  // Функция для инициализации и получения номера при каждом клике
  function getMangoNumberOnClick(orgId, phone, callback) {
    // Создаем уникальный хэш для каждого запроса
    const uniqueHash = 'org_' + orgId + '_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
   mgo({
      calltracking: {
        id: 36238,
        elements: [{selector: '.mgo-call-button'}],
        customParam: 'organization_id=' + orgId + '&hash=' + uniqueHash
      }
    });


    // Запрашиваем номер
    mgo.getNumber({
      hash: uniqueHash,
      redirectNumber: phone
    }, function(result) {
      if (result && result.number) {
        callback(result.number);
      } else {
        callback(phone);
      }
    });

// Инициализируем Mango Office с уникальными параметрами для каждого вызова

  }

  // Функция для проверки мобильного устройства
  function isMobileDevice() {
    return (typeof window.orientation !== "undefined") || (navigator.userAgent.indexOf('IEMobile') !== -1);
  }

  // Функция для копирования текста в буфер обмена
  function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(text).then(function() {
        showNotification('Номер скопирован!');
      }).catch(function() {
        fallbackCopyToClipboard(text);
      });
    } else {
      fallbackCopyToClipboard(text);
    }
  }

  function fallbackCopyToClipboard(text) {
    const input = document.createElement('input');
    input.style.position = 'fixed';
    input.style.opacity = 0;
    input.value = text;
    document.body.appendChild(input);
    input.select();
    document.execCommand('copy');
    document.body.removeChild(input);
    showNotification('Номер скопирован!');
  }

  function showNotification(message) {
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
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
      document.body.removeChild(notification);
    }, 2000);
  }

  // Создаем модальное окно
  const modalOverlay = document.createElement('div');
  modalOverlay.className = 'mgo-modal-overlay';
  modalOverlay.style.display = 'none';

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
    setTimeout(() => {
      modalOverlay.style.display = 'none';
    }, 300);
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

  // Основная функция обработки клика
  function handleCallButtonClick(button) {
    let calls = parseInt(button.getAttribute('data-calls'));
    let orgId = button.getAttribute('data-org-id');
    let phone = button.getAttribute('data-phone');


    // Показываем загрузку
    const originalText = button.innerHTML;
    button.innerHTML = 'Загрузка...';
    button.style.pointerEvents = 'none';

    if (calls == 1) {
      // Получаем номер через Mango Office (инициализация + запрос каждый раз)
      getMangoNumberOnClick(orgId, phone, function(mangoNumber) {
        if (mangoNumber) {
          showModal(mangoNumber);
        } else {
          showModal(phone);
        }

        // Восстанавливаем кнопку
        button.innerHTML = originalText;
        button.style.pointerEvents = 'auto';
      });
    } else {
      setTimeout(function() {
        button.innerHTML = originalText;
        button.style.pointerEvents = 'auto';
        showAltModal();
      }, 1500);
    }
  }

  // Обработчик для кнопок "Позвонить"
  document.addEventListener('DOMContentLoaded', function() {
    // Обработчик для существующих кнопок
    document.querySelectorAll('.mgo-call-button').forEach(function(button) {
      button.addEventListener('click', function() {
        handleCallButtonClick(this);
      });
    });

    // Для динамически добавляемых кнопок через AJAX
    const observer = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        mutation.addedNodes.forEach(function(node) {
          if (node.nodeType === 1) {
            const newButtons = node.querySelectorAll ? node.querySelectorAll('.mgo-call-button') : [];
            newButtons.forEach(function(button) {
              button.addEventListener('click', function() {
                handleCallButtonClick(this);
              });
            });
          }
        });
      });
    });

    observer.observe(document.body, {
      childList: true,
      subtree: true
    });
  });

  // Код для альтернативного модального окна
  const altModalOverlay = document.createElement('div');
  altModalOverlay.className = 'mgo-modal-overlay';
  altModalOverlay.style.display = 'none';

  const altModal = document.createElement('div');
  altModal.className = 'mgo-alternatives-modal';

  const alternativeOrgs = [
    // Ваш массив альтернативных организаций
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

    altModal.querySelectorAll('.mgo-alternative-call').forEach(btn => {
      btn.addEventListener('click', function() {
        const phone = this.getAttribute('data-phone');
        const orgId = this.getAttribute('data-org-id');
        closeAltModal();

        // Создаем временную кнопку для обработки вызова
        const tempButton = document.createElement('button');
        tempButton.setAttribute('data-calls', '1');
        tempButton.setAttribute('data-org-id', orgId);
        tempButton.setAttribute('data-phone', phone);
        handleCallButtonClick(tempButton);
      });
    });

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

  document.body.appendChild(altModalOverlay);
  document.body.appendChild(altModal);

</script>


@endif




