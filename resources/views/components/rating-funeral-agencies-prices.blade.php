<?php 
use App\Models\ActivityCategoryOrganization;

$city = selectCity();
$organizations = organizationRatingFuneralAgenciesPrices($city->id);
?>

@if($organizations != null && isset($organizations[1]) && $organizations->count() > 0 && $organizations->first() != null)
<?php
// Предзагрузка всех цен для организаций
$organizationIds = $organizations->pluck('id');
$prices = ActivityCategoryOrganization::whereIn('category_children_id', [32, 33, 34])
    ->whereIn('organization_id', $organizationIds)
    ->get()
    ->groupBy(['organization_id', 'category_children_id']);
?>
<section class="raiting">
    <div class="container">
        <h2 class="title_our_works">Рейтинг ритуальных агентств в г. {{$city->title}}: 10 лучших предложений по ценам</h2>
        <div class="text_block">* Цены являются приблизительными. Уточняйте стоимость, позвонив в агентство.</div>
        
    <div class="table_rating_block">
        <table class="raiting_table">
            <thead>
                <tr>
                    <th>Агентство</th>
                    <th>Похороны</th>
                    <th>Кремация</th>
                    <th>Отправка груз 200</th>
                </tr>
            </thead>
            <tbody>
                @foreach($organizations as $organization)
                    @if($organization != null)
                        <?php 
                            $price_1 = $prices[$organization->id][32]->first() ?? null;
                            $price_2 = $prices[$organization->id][33]->first() ?? null;
                            $price_3 = $prices[$organization->id][34]->first() ?? null;
                        ?>
                        <tr>
                            <td class='name_organization'>
                                @if($organization->urlImg() == 'default')
                                    <img class='white_img_org img_logo_organization' src="{{$organization->defaultLogoImg()[0]}}" alt="">   
                                    <img class='black_img_org img_logo_organization' src="{{$organization->defaultLogoImg()[1]}}" alt="">   
                                @else
                                    <img src="{{$organization->urlImg()}}" alt="">   
                                @endif
                                <a href='{{$organization->route()}}' class="title_organization">{{$organization->name_type}}: 
                                    "{{$organization->title}}"</a>
                            </td>
                            <td>
                                <div class="text_black">
                                    @if($price_1 && (strpos($price_1->priceHtml(), 'Уточняйте') !== false || strpos($price_1->priceHtml(), 'Уточняйте') !== false))
                                        @if($organization->city->edge->call_mango_office)
                                            <a href='javascript:void(0)' class="mgo-call-button price-link" 
                                                data-key="{{ 1 }}"
                                                data-org-id="{{ $organization->id }}"
                                                data-phone="{{ str_replace('+', '', $organization->phone) }}"
                                                data-default-number="{{ $organization->phone }}"
                                                data-calls="{{ $organization->haveCalls() }}">
                                                Уточняйте
                                            </a>
                                        @else
                                            <a href='tel:{{ $organization->phone }}' class=" price-link"> 
                                                Уточняйте
                                            </a>
                                        @endif
                                    @else
                                        {{ $price_1 ? $price_1->priceHtml() : '—' }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="text_black">
                                    @if($price_2 && (strpos($price_2->priceHtml(), 'Уточняйте') !== false || strpos($price_2->priceHtml(), 'Уточняйте') !== false))
                                        @if($organization->city->edge->call_mango_office)
                                            <a href='javascript:void(0)' class="mgo-call-button price-link" 
                                                data-key="{{ 1 }}"
                                                data-org-id="{{ $organization->id }}"
                                                data-phone="{{ str_replace('+', '', $organization->phone) }}"
                                                data-default-number="{{ $organization->phone }}"
                                                data-calls="{{ $organization->haveCalls() }}">
                                                Уточняйте
                                            </a>
                                        @else
                                            <a href='tel:{{ $organization->phone }}' class=" price-link"> 
                                                Уточняйте
                                            </a>
                                        @endif
                                    @else
                                        {{ $price_2 ? $price_2->priceHtml() : '—' }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="text_black">
                                    @if($price_3 && (strpos($price_3->priceHtml(), 'Уточняйте') !== false || strpos($price_3->priceHtml(), 'Уточняйте') !== false))
                                        @if($organization->city->edge->call_mango_office)
                                            <a href='javascript:void(0)' class="mgo-call-button price-link" 
                                                data-key="{{ 1 }}"
                                                data-org-id="{{ $organization->id }}"
                                                data-phone="{{ str_replace('+', '', $organization->phone) }}"
                                                data-default-number="{{ $organization->phone }}"
                                                data-calls="{{ $organization->haveCalls() }}">
                                                Уточняйте
                                            </a>
                                        @else
                                            <a href='tel:{{ $organization->phone }}' class=" price-link"> 
                                                Уточняйте
                                            </a>
                                        @endif
                                    @else
                                        {{ $price_3 ? $price_3->priceHtml() : '—' }}
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    </div>
</section>

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
