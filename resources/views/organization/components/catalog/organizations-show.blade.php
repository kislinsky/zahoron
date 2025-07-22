
@if($organizations_category!=null && $organizations_category->total()>0)

    <?php $category=$organizations_category->first()->categoryProduct;?>

    @foreach ($organizations_category as $organization_category)

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
                <a href='tel:{{$organization->phone}}'class="blue_btn">Позвонить</a>
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
    
@endif




