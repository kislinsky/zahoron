


<footer class='bac_gray'>
    <div class="container">
      <img src="{{asset('storage/uploads/Frame (19).svg')}}" alt="" class="rose_footer img_light_theme">
      <img src="{{asset('storage/uploads/Frame (4)_black.svg')}}" alt="" class="rose_footer img_black_theme">
      
       <div class="flex_footer">
          <a class='logo' href='/'>
            <img class='img_light_theme' src='{{asset('storage/uploads/zahoron.svg')}}'>
            <img class='img_black_theme' src="{{asset('storage/uploads/РИТУАЛреестр.svg')}}" alt="">         </a>
         <div class='pages'>
            <a href='{{ route('page.search.burial.filter') }}'class="no_bac_btn">Герои </a>
            <a href='{{ route('marketplace') }}'class="no_bac_btn">Маркетплейс</a>
            <a href='{{ route('page.search.burial.request') }}'class="no_bac_btn">Заявка на поиск</a>
            <a href='{{ route('our.products') }}'class="no_bac_btn">Наши работы</a>
            <a href='{{ route('cemeteries') }}'class="no_bac_btn">Кладбища </a>
            <a href='{{ route('news') }}'class="no_bac_btn">Статьи</a>
            <a href='{{ route('contacts') }}'class="no_bac_btn">Контакты</a>
            <a href='#'class="no_bac_btn">Партнерам</a>
         </div>
       </div>
 
 
       <div class="flex_footer">
          <div class="flex_icons_footer">
             <a href="#"><img src="{{asset('storage/uploads/socials.svg')}}" alt=""></a>
             <a href="#"><img src="{{asset('storage/uploads/vk (3).svg')}}" alt=""></a>
             <a href="#"><img src="{{asset('storage/uploads/socials (1).svg')}}" alt=""></a>
             <a href="#"><img src="{{asset('storage/uploads/socials (2).svg')}}" alt=""></a>
          </div>
          <div class="flex_footer_2">
             <p>Мы принимаем</p>
             <img src="{{asset('storage/uploads/image 3.svg')}}" alt="">
             <img src="{{asset('storage/uploads/visa-secure_blu_2021_dkbg 1.svg')}}" alt="">
             <img src="{{asset('storage/uploads/image 4.svg')}}" alt="">
             <img src="{{asset('storage/uploads/image 5 (1).svg')}}" alt="">
          </div>
       </div>
       <div class="block_info_footer">
          ИНДИВИДУАЛЬНЫЙ ПРЕДПРИНИМАТЕЛЬ КИСЛИНСКИЙ АЛЕКСАНДР ВАЛЕРЬЕВИЧ<br>
          ОГРН: 314410117400027<br>
          ИНН: 370253115213<br>
          Юридический адрес: Россия, Камчатский край , Елизовский район, г. Елизово ул. Рябикова д. 16, 684000
       </div>
 
       <div class="flex_footer">
          <div class="flex_footer_2">
             Свяжитесь с нами
             <div class="flex_mini_icons_footer">
                <a href="#"><img src="{{asset('storage/uploads/socials (3).svg')}}" alt=""></a>
                <a href="#"><img src="{{asset('storage/uploads/socials (4).svg')}}" alt=""></a>
                <a href="#"><img src="{{asset('storage/uploads/socials (5).svg')}}" alt=""></a>
             </div>
             <a href='mailto:info@zahoron.ru'>info@zahoron.ru</a>
          </div>
          <div class="blue_btn">Напишите нам</div>
       </div>
       
      <div>
         <div class="block_info_footer">Все права защищены © <?php echo date("Y"); ?> Ритуал.реестр</div>
         <a href='{{ route('terms-user') }}' class="block_info_footer">Пользовательское соглашение</a>
      </div>
   </div>
 </footer>
 


<script src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
<script type="text/javascript" src="{{asset('js/main.js')}}"></script>


<script>
   $( ".block_ajax_input_search_cities .input_search_cities" ).on( "input", function() {
         let city_id_input=$(this).siblings('.city_id_input')
         let this_block=$(this).parent('.block_ajax_input_search_cities')
         console.log($(this).val())
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
</script>

</body>
</html>