
@if($organizations_category!=null && $organizations_category->total()>0)

    <?php $category=$organizations_category->first()->categoryProduct;?>

    @foreach ($organizations_category as $key=>$organization_category)

        <?php $organization=$organization_category->organization;?>
    
        

        <div class="li_organization">

            <?php $category_organiaztion=$organization_category->categoryProduct;?>


            <div class="info_li_org_mobile li_org_mobile">
                
                @if($organization->urlImgMain()=='default')
                    <img class='white_img_org img_logo_organization' src="{{$organization->defaultMainImg()[0]}}" alt="">   
                    <img class='black_img_org img_logo_organization' src="{{$organization->defaultMainImg()[1]}}" alt="">   
                @else
                    <img class='img_logo_organization' src="{{$organization->urlImgMain()}}" alt="">   
                @endif

                <div class="info_li_organization">
                    <a href='{{$organization->route()}}'class="title_li_organiaztion">{{$organization->title}}</a>
                    <div class="text_gray">{{$organization->name_type}}</div>
                    <div class="text_gray"> {{$organization->adres}}</div>
                </div>
            </div>

            <div class="info_li_org_mobile li_org_mobile"><div class="text_black"><img src="{{ asset('storage/uploads/Frame 334.svg') }}" alt=""> {{ $organization->rating }} - {{countReviewsOrganization($organization)}} оценок</div>   <div class="text_black">{{$organization->timeEndWorkingNow()}}</div></div>


            <div class="li_logo_organization li_org_dekstop">
                @if($organization->urlImg()=='default')
                    <img class='white_img_org img_logo_organization' src="{{$organization->defaultLogoImg()[0]}}" alt="">   
                    <img class='black_img_org img_logo_organization' src="{{$organization->defaultLogoImg()[1]}}" alt="">   
                @else
                    <img class='img_logo_organization' src="{{$organization->urlImg()}}" alt="">   
                @endif
                <div class="flex_stars">
                    <img src="{{asset('storage/uploads/Frame 334.svg')}}" alt=""> <div class="text_black">{{$organization->rating}}</div>
                </div>
            </div>

            <div class="info_li_organization li_org_dekstop">
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
                    <a href="{{route('organization.like.add',$organization->id)}}"><img src="{{asset('storage/uploads/Vector (9).svg')}}" alt=""></a>
                    <div val='{{ $organization->route() }}' class='share_button'><img src="{{asset('storage/uploads/Vector (8).svg')}}" alt=""></div>
                </div>
            </div>

            <div class="li_price_category_organization li_org_dekstop">
                <div class="text_gray">{{$category_organiaztion->title}}</div>
                <div class="title_blue"> {{$organization_category->priceHtml()}} </div>
            </div>

            <div class="li_flex_btn_organization">
                 <a href='javascript:void(0)' class="blue_btn mgo-call-button" 
                   data-key="{{ $key }}"
                   data-org-id="{{ $organization->id }}"
                   data-phone="{{ str_replace('+', '', $organization->phone) }}"
                   data-default-number="{{ $organization->phone }}"
                   data-calls="{{ $organization->calls }}"
                  > 
                    Позвонить
                </a>
                <a href='{{$organization->route()}}' class="btn_border_blue">Подробнее</a>
            </div>

            <div class="li_flex_icon_organization li_org_dekstop">
                <a href="{{route('organization.like.add',$organization->id)}}"><img src="{{asset('storage/uploads/Vector (9).svg')}}" alt=""></a>
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
        
        // Показываем загрузку
        this.innerHTML = 'Загрузка...';
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
                button.innerHTML = formattedNumber;
                button.setAttribute('href', 'tel:+' + n);
                button.style.pointerEvents = 'auto';
                
                // Инициируем звонок
                window.location.href = 'tel:+' + n;
              } else {
                
              }
            });
          });
        }else{
          setTimeout(function() {
            button.innerHTML = 'Ошибка';
            console.log('Недостаточно звонков')
          }, 1500);
        }
        
      });
    });
  });
</script>
@endif




