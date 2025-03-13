


<div class="block_inpit_form_search info_contacts_settings_organization">
    <div class="title_middle">Контактная информация</div>
    <div class="flex_input_form_contacts">
        <div class="block_input">
            <label for="">Телефон</label>
            <input placeholder='+758435348053' type="text" name='phone' value='{{$organization->phone}}'>
        </div>
        <div class="block_input">
            <label for="">WhatsApp</label>
            <input placeholder='+758435348053' type="text" name='whatsapp' value='{{$organization->whatsapp}}'>
        </div>
    </div>
    <div class="flex_input_form_contacts">
        <div class="block_input">
            <label for="">Telegram</label>
            <input placeholder='+758435348053' type="text" name='telegram' value='{{$organization->telegram}}'>
        </div>
        <div class="block_input">
            <label for="">Email</label>
            <input placeholder='zahoron@gmail.com' type="email" name='email' value='{{$organization->email}}'>
        </div>
    </div>
    <div class="block_input">
        <label for="">Город</label>
        <div class="block_ajax_input_search_cities">
            <input class='input_search_cities' type="text" name="city_search" id="" value='{{ $organization->city->title }}'>
            <input type="hidden" name="city_id" class='city_id_input'  value='{{ $organization->city_id }}' >
        </div>
    </div>
    <div class="block_input">
        <label for="">Адрес агенства</label>
        <input placeholder='Москва, улица Льва Толстого, дом 16.' type="text" name='adres' value='{{$organization->adres}}'>
    </div>

    <div class="block_input">
        
        <div width_new='' longitude_new='' id="map_organization_single" style="width: 100%; height: 400px"></div>

        <div class="flex_btn">
            <div class="blue_btn open_map_organization">Закрепить раположение места</div>
            <div class="gray_btn open_coordinates_organization">Введите координаты вручную</div>
        </div>

        <div class="coordinates_organization">
            <div class="flex_input_form_contacts">
                <div class="block_input">
                    <label for="">Шириина</label>
                    <input placeholder='' type="text" name='width' value='{{$organization->width}}'>
                </div>
                <div class="block_input">
                    <label for="">Долгота</label>
                    <input placeholder='' type="text" name='longitude' value='{{$organization->longitude}}'>
                </div>
            </div>
        </div>

    </div>

    <div class="block_input">
        <label for="">Рядом с </label>
        <input placeholder='ул. Филимонова' type="text" name='next_to' value='{{$organization->next_to}}'>
    </div>
    <div class="block_input">
        <label for="">Метро</label>
        <input placeholder='ст. Комсомольская' type="text" name='underground' value='{{$organization->underground}}'>
    </div>
</div>

<script>

ymaps.ready(init);

function init() {
    var myMap = new ymaps.Map("map_organization_single", {
            center: [{{  $organization->width}}, {{$organization->longitude}}],
            zoom: 10
        });

        myMap.controls.remove('searchControl');
        
      myMap.geoObjects.add(new ymaps.Placemark(['{{$organization->width}}', '{{$organization->longitude}}'], {
            balloonContent: '{!!$organization->title!!}',
            iconCaption: '{!!$organization->title!!}'
        },));

        
        let searchControl = new ymaps.control.SearchControl({
            options: {
            provider: 'yandex#search'
            }
        });

  // Добавим поиск на карту
  myMap.controls.add(searchControl);

  // Нужное нам событие (выбор результата поиска)
  searchControl.events.add('resultselect', function(e) {
    var index = e.get('index');
    searchControl.getResult(index).then(function(res) {

        $('#map_organization_single').attr('width_new',res.geometry.getCoordinates()[0])
        $('#map_organization_single').attr('longitude_new',res.geometry.getCoordinates()[1])

    });
  })
}




$( ".open_map_organization" ).on( "click", function() {
    $(this).removeClass('gray_btn')
    $(this).addClass('blue_btn')
    $('input[name="width"]').val($('#map_organization_single').attr('width_new'));
    $('input[name="longitude"]').val( $('#map_organization_single').attr('longitude_new'));

})

$( ".open_coordinates_organization" ).on( "click", function() {
    $('.coordinates_organization').toggle()
    $(this).toggleClass('gray_btn')
    $(this).toggleClass('blue_btn')
})

</script>
