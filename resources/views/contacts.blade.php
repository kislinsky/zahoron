@include('header.header')

<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <h1 class="index_title">{!! $title_h1 !!}</h1>    
        </div>
        <img class='img_light_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        <img class='img_black_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1_black.svg')}}" alt="">
    </div>
</section>

<section class="contacts">
    <div class="container">
        <div class="content_news_single">
            <div class="flex_contacts adres_contacts">
                Юридический адрес: Россия, Камчатский край , Елизовский район, г. Елизово ул. Рябикова д. 16, 684000
            </div>

            <div class="block_conatcts">
                <div class="flex_contacts">email: <a href="mailto:kislinsky@mail.ru">kislinsky@mail.ru</a></div>
                <div class="flex_contacts">telegram: <a href="https://t.me/Alexandr1980kam">https://t.me/Alexandr1980kam</a></div>
            </div>
            <div class="block_conatcts">
                <div class="flex_contacts">ИНДИВИДУАЛЬНЫЙ ПРЕДПРИНИМАТЕЛЬ КИСЛИНСКИЙ АЛЕКСАНДР ВАЛЕРЬЕВИЧ</div>
                <div class="flex_contacts">Расчётный счёт: 40802810336170007227</div>
                <div class="flex_contacts">Банк: СЕВЕРО-ВОСТОЧНОЕ ОТДЕЛЕНИЕ N8645 ПАО СБЕРБАНК</div>
                <div class="flex_contacts">БИК: 044442607</div>
                <div class="flex_contacts">Кор. Cчёт: 30101810300000000607</div>
                <div class="flex_contacts">ОГРН: 314410117400027</div>
                <div class="flex_contacts">ИНН: 370253115213</div>
            </div>
        </div>
    </div>
</section>

@include('components.faq') 


<section class="karta_contacts">
    <div class="container">
        <h2 class="title_our_works">Офис на карте</h2>
        <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3Af6b2445394efe4343b6369062f2e9092bca16e76ec6dcc4b831f9c84b18cd5a8&amp;source=constructor" width="100%" height="530" style='margin-top:20px;' frameborder="0"></iframe>
   </div>
</section>

@include('forms.contacts-form') 


<section>
    <div class="container">
        <div class="content_news_single">

            В современной жизни у нас не всегда есть возможность часто приходить на могилу и ухаживать за ней. Кто-то живет далеко от кладбища, у кого-то попросту нет времени, а у кого-то и желания. Причин может быть много, но результат всегда один: со временем место захоронения зарастает травой, памятник покрывается пылью и дождевыми подтеками, а сама территория начинает выглядеть неухоженной и заброшенной.<br><br>Однако вопрос уборки места захоронения всегда довольно важен: это не только дань памяти усопшему, но и вопрос нашей совести. И в том случае, если у Вас нет возможности самостоятельно приехать и навести порядок на месте захоронения, Вы можете воспользоваться услугами компании "Городские ритуальные услуги".<br><br><strong>Наши мастера окажут любые услуги, связанные с уборкой могилы:</strong>
            <ul>
                <li>вырвут сорняки;</li>
                <li>соберут мусор;</li>
                <li>посадят цветы;</li>
                <li>окрасят ограду;</li>
                <li>покрасят ограду;</li>
                <li>заменят старые искусственные цветы, венки или корзины на новые;</li>
                <li>поменяют выцветшую фотографию;</li>
                <li>зажгут лампадки и свечи;</li>
                <li>помоют памятник;</li>
                <li>уберут листву или снег;</li>
                <li>постригут траву;</li>
                <li>выровняют участок вокруг надгробия;</li>
                <li>и многое другое.</li>
            </ul>
            <br><br>
            Работы могут проводиться в разное время года и в любое удобное для Вас время. Вы можете заказать уборку могилы как разовую, например, к определенной дате (годовщине смерти или поминальному дню), так и комплексную – на месяц или на год.<br><br>Наши рабочие всегда компетентно и ответственно выполнят всю работу и в случае необходимости сделают фотографии "до" и "после" уборки, чтобы Вы точно убедились в их работе.<br><br>Стоимость услуг зависит от объема работ, срочности заказа и требуемых результатов, поэтому все вопросы по поводу цены Вы можете узнать по контактному телефону.<br><br><h3>ВАЖНО ПОМНИТЬ!</h3>Содержание могилы и захоронения в надлежащем виде является Вашей обязанностью! При отсутствии надлежащего ухода за местами захоронения они признаются бесхозными. Поэтому всегда следует помнить об их уборке и приводить их в надлежащий вид если не собственными силами, то силами компаний, оказывающих услуги по уборке могилы.
        </div>
    </div>
</section>


@include('forms.search-form') 


@include('footer.footer') 
