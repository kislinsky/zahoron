<div class="block_inpit_form_search info_contacts_settings_organization">
    <div class="title_middle">Контактная информация</div>
    <div class="flex_input_form_contacts">
        <div class="block_input">
            <label for="">Телефон</label>
            <input placeholder='+758435348053' type="text" name='phone' value='{{$organization->phone ?? ''}}'>
        </div>
        <div class="block_input">
            <label for="">WhatsApp</label>
            <input placeholder='+758435348053' type="text" name='whatsapp' value='{{$organization->whatsapp ?? ''}}'>
        </div>
    </div>
    <div class="flex_input_form_contacts">
        <div class="block_input">
            <label for="">Telegram</label>
            <input placeholder='+758435348053' type="text" name='telegram' value='{{$organization->telegram ?? ''}}'>
        </div>
        <div class="block_input">
            <label for="">Email</label>
            <input placeholder='zahoron@gmail.com' type="email" name='email' value='{{$organization->email ?? ''}}'>
        </div>
    </div>
    <div class="block_input">
        <label for="">Город</label>
        <div class="block_ajax_input_search_cities">
            <input class='input_search_cities' type="text" name="city_search" id="" value='{{ $organization->city->title ?? ''}}'>
            <input type="hidden" name="city_id" class='city_id_input'  value='{{ $organization->city_id ?? ''}}' >
        </div>
    </div>
    <div class="block_input">
        <label for="">Адрес агенства</label>
        <input placeholder='Москва, улица Льва Толстого, дом 16.' type="text" name='adres' value='{{$organization->adres ?? ''}}'>
    </div>

    <div class="block_input">

        <div id="map_organization_single" style="width: 100%; height: 400px"
             data-width-new='{{$organization->width ?? ''}}'
             data-longitude-new='{{$organization->longitude ?? ''}}'>
        </div>

        <div class="flex_btn">
            <div class="gray_btn open_coordinates_organization">Ввести координаты вручную</div>
        </div>

        <div class="coordinates_organization">
            <div class="flex_input_form_contacts">
                <div class="block_input">
                    <label for="">Широта</label>
                    <input placeholder='' type="text" name='width' value='{{$organization->width ?? ''}}'>
                </div>
                <div class="block_input">
                    <label for="">Долгота</label>
                    <input placeholder='' type="text" name='longitude' value='{{$organization->longitude ?? ''}}'>
                </div>
            </div>
        </div>

    </div>

    <div class="block_input">
        <label for="">Рядом с </label>
        <input placeholder='ул. Филимонова' type="text" name='next_to' value='{{$organization->next_to ?? ''}}'>
    </div>
    <div class="block_input">
        <label for="">Метро</label>
        <input placeholder='ст. Комсомольская' type="text" name='underground' value='{{$organization->underground ?? ''}}'>
    </div>
</div>

<script>
    let myMap;
    let currentPlacemark = null;
    let searchTimeout;
    let geocodeTimeout;

    ymaps.ready(init);

    function init() {
        const initialWidth = $('#map_organization_single').data('width-new');
        const initialLongitude = $('#map_organization_single').data('longitude-new');

        const defaultCenter = [55.7558, 37.6173];

        const mapCenter = (initialWidth && initialLongitude)
            ? [parseFloat(initialWidth), parseFloat(initialLongitude)]
            : defaultCenter;

        // Инициализация карты
        myMap = new ymaps.Map("map_organization_single", {
            center: mapCenter,
            zoom: (initialWidth && initialLongitude) ? 14 : 10
        });

        myMap.controls.remove('searchControl');

        // Добавление метки, только если координаты существуют
        if (initialWidth && initialLongitude) {
            currentPlacemark = createPlacemark(mapCenter, '{!! $organization->title ?? '' !!}');
            myMap.geoObjects.add(currentPlacemark);
            reverseGeocodeAndUpdate(mapCenter);

            // Автоматически записываем начальные координаты в поля ввода
            updateCoordinateInputs(mapCenter);
        }

        // Установка метки по клику на карту
        myMap.events.add('click', function (e) {
            const coords = e.get('coords');

            $('#map_organization_single').data('width-new', coords[0]);
            $('#map_organization_single').data('longitude-new', coords[1]);

            updatePlacemark(coords, '');

            if (currentPlacemark && currentPlacemark.balloon.isOpen()) {
                currentPlacemark.balloon.close();
            }
        });

        // Добавление стандартного поиска Яндекса
        let searchControl = new ymaps.control.SearchControl({
            options: {
                provider: 'yandex#search'
            }
        });
        myMap.controls.add(searchControl);

        // Обработка выбора результата из поиска Яндекса
        searchControl.events.add('resultselect', function(e) {
            var index = e.get('index');
            searchControl.getResult(index).then(function(res) {
                const newCoords = res.geometry.getCoordinates();

                $('#map_organization_single').data('width-new', newCoords[0]);
                $('#map_organization_single').data('longitude-new', newCoords[1]);

                updatePlacemark(newCoords, res.properties.get('name'));
            });
        });
    }

    /**
     * Записывает координаты в поля ввода 'width' (широта) и 'longitude' (долгота).
     */
    function updateCoordinateInputs(coords) {
        const newLatitude = coords[0].toFixed(6);
        const newLongitude = coords[1].toFixed(6);

        $('input[name="width"]').val(newLatitude);
        $('input[name="longitude"]').val(newLongitude);
    }


    /**
     * Вспомогательная функция для создания перетаскиваемой метки
     */
    function createPlacemark(coords, title) {
        const placemark = new ymaps.Placemark(coords, {
            balloonContent: title,
            iconCaption: title
        }, {
            draggable: true
        });

        // Обработчик события после окончания перетаскивания
        placemark.events.add('dragend', function (e) {
            const newCoords = placemark.geometry.getCoordinates();

            $('#map_organization_single').data('width-new', newCoords[0]);
            $('#map_organization_single').data('longitude-new', newCoords[1]);

            // Запись координат после перетаскивания
            updateCoordinateInputs(newCoords);
            // ------------------------------------------

            // Обновляем адрес после перетаскивания
            reverseGeocodeAndUpdate(newCoords);
        });

        return placemark;
    }

    /**
     * Вспомогательная функция для обновления метки (перемещение или создание)
     */
    function updatePlacemark(coords, title) {
        if (currentPlacemark) {
            currentPlacemark.geometry.setCoordinates(coords);
        } else {
            const defaultTitle = '{!! $organization->title ?? '' !!}';
            currentPlacemark = createPlacemark(coords, title || defaultTitle);
            myMap.geoObjects.add(currentPlacemark);
        }

        const currentZoom = myMap.getZoom();
        myMap.setCenter(coords, currentZoom, {duration: 300});

        // Запись координат
        updateCoordinateInputs(coords);
        // ------------------------------------------

        // Обновляем адрес
        reverseGeocodeAndUpdate(coords);
    }

    /**
     * Получает адрес по координатам и обновляет содержимое метки
     */
    function reverseGeocodeAndUpdate(coords, delay = 0) {
        clearTimeout(geocodeTimeout);

        geocodeTimeout = setTimeout(function() {
            ymaps.geocode(coords).then(function (res) {
                const firstGeoObject = res.geoObjects.get(0);
                if (firstGeoObject) {
                    const addressDetails = firstGeoObject.properties.get('metaDataProperty.GeocoderMetaData.Address.Components');

                    let city = '';
                    let street = '';

                    addressDetails.forEach(function(component) {
                        if (component.kind === 'locality' || component.kind === 'city') {
                            city = component.name;
                        }
                        if (component.kind === 'street' || component.kind === 'house') {
                            street = (street ? street + ', ' : '') + component.name;
                        }
                    });

                    let shortAddress = city;
                    if (street) {
                        shortAddress = (city ? city + ', ' : '') + street;
                    }

                    const fullAddress = firstGeoObject.getAddressLine();
                    const shortDescription = firstGeoObject.properties.get('text');

                    if (currentPlacemark) {
                        currentPlacemark.properties.set({
                            balloonContent: '<b>Текущее расположение:</b><br>' + fullAddress,
                            iconCaption: shortDescription
                        });
                    }

                    // Записываем адрес в инпут
                    $('input[name="adres"]').val(shortAddress);
                }
            });
        }, delay);
    }

    // Поиск адреса при вводе в поле "Адрес агенства"
    $('input[name="adres"]').on('keyup', function() {
        const address = $(this).val();
        clearTimeout(searchTimeout);

        if (address.length < 5) {
            return;
        }

        searchTimeout = setTimeout(function() {
            ymaps.geocode(address, {
                results: 1
            }).then(function (res) {

                const firstGeoObject = res.geoObjects.get(0);

                if (firstGeoObject) {
                    const newCoords = firstGeoObject.geometry.getCoordinates();

                    $('#map_organization_single').data('width-new', newCoords[0]);
                    $('#map_organization_single').data('longitude-new', newCoords[1]);
                    updatePlacemark(newCoords, firstGeoObject.properties.get('name'));
                } else {
                    console.log('Адрес не найден: ' + address);
                }
            });
        }, 500);
    });

    $( ".open_coordinates_organization" ).on( "click", function() {
        $('.coordinates_organization').toggle()
        $(this).toggleClass('gray_btn')
        $(this).toggleClass('blue_btn')
    })
</script>
