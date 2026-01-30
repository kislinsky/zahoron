


<footer class='footer_blue'>
   <div class="container">
        <div class="ul_pages_footer">
            <div class="title_page_footer">О нас</div>
            <div class="li_page_footer">Политика обработки персональных данных</div>
            <div class="li_page_footer">Пользовательское соглашение</div>
            <div class="li_page_footer">Партнерам</div>
            <div class="li_page_footer">Личный кабинет партнера</div>
            <div class="li_page_footer">Размещение объектов</div>
            <div class="li_page_footer">Контакты</div>
        </div>
        <div class="ul_pages_footer">
            <div class="title_page_footer">Ритуальные услуги</div>
            <div class="li_page_footer">Организация похорон</div>
            <div class="li_page_footer">Цены на кремацию</div>
            <div class="li_page_footer">Груз 200 Россия</div>
            <div class="li_page_footer">Эксгумация</div>
            <div class="li_page_footer">Усыпление животных</div>
            <div class="li_page_footer">Кремация животных</div>
        </div>
        <div class="ul_pages_footer">
            <div class="title_page_footer">Индивидуальный предприниматель<br>
Кислинский Александр Валерьевич</div>
            <div class="li_page_footer">
                <p>ОГРН: 314410117400027</p><br>
                <p>ИНН: 370253115213</p>
            </div>
            <div class="li_page_footer">info@zahoron.ru</div>
            <div class="li_page_footer">Россия, Камчатский край, г. Елизово, ул. Рябикова д. 16, 684000</div>
            <div class="flex_icons_footer">
                <a href><img src="{{ asset('storage/uploads/Ellipse 23.svg') }}" alt=""></a>
                <a href=""><img src="{{ asset('storage/uploads/Vector (25).svg') }}" alt=""></a>
            </div>
            <div class="btn_white">
                Задать вопрос
            </div>
        </div>
   </div>
 </footer>
 
{!! get_acf(20,'footer') !!}
<script defer src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js" type="text/javascript"></script>
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script defer src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
<script defer type="text/javascript" src="{{asset('js/main.js')}}"></script>
<script async src="//widgets.mango-office.ru/site/36238"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
   $( ".block_ajax_input_search_cities .input_search_cities" ).on( "input", function() {
         let city_id_input=$(this).siblings('.city_id_input')
         let this_block=$(this).parent('.block_ajax_input_search_cities')
        let data  = {
            "_token": "{{ csrf_token() }}",
            's':$(this).val(),
        };

        $.ajax({
            type: 'POST',
            url: '{{route('ajax.cities.search.input')}}',
            data:  data,
            success: function (result) {
               $('.abs_cities_input_search').remove()
               this_block.append(result)
            },
            error: function () {
               $('.abs_cities_input_search').remove()
            }
        });
   })

// Глобальный обработчик reCAPTCHA
window.initRecaptcha = function() {
    document.querySelectorAll('.g-recaptcha').forEach(el => {
        if (!el.dataset.rendered) {
            grecaptcha.render(el, {
                sitekey: el.dataset.sitekey,
                callback: function(response) {
                    el.closest('form').querySelector('[name="g-recaptcha-response"]').value = response;
                }
            });
            el.dataset.rendered = true;
        }
    });
};

// Ленивая загрузка при первом взаимодействии с любой формой
function loadRecaptchaOnDemand() {
    if (!window.recaptchaLoaded) {
        window.recaptchaLoaded = true;
        const script = document.createElement('script');
        script.src = `https://www.google.com/recaptcha/api.js?render=explicit&onload=initRecaptcha`;
        script.async = true;
        document.head.appendChild(script);
        
        // Отключаем все обработчики
        document.querySelectorAll('input, textarea, select').forEach(el => {
            el.removeEventListener('focus', loadRecaptchaOnDemand);
        });
    }
}

// Инициализация
document.addEventListener('DOMContentLoaded', function() {
    // Вешаем на все поля форм
    document.querySelectorAll('form input, form textarea').forEach(el => {
        el.addEventListener('focus', loadRecaptchaOnDemand, { once: true });
    });
    
    // Альтернатива: загрузка при скролле до любой формы
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                loadRecaptchaOnDemand();
            }
        });
    }, { threshold: 0.1 });
    
    document.querySelectorAll('form').forEach(form => {
        observer.observe(form);
    });
});
</script>
</body>
</html>