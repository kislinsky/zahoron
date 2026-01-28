@include('header.header')
<section class="order_page bac_gray">
    <div class="container order_page_search">
        <div class="content_order_page">
            <h1 class="index_title">Результаты поиска</h1>    
            <form method='get' action="{{route('search.burial.result')}}" class="search">
                @csrf
                <input type="text" name='surname' placeholder='Фамилия'>
                <input type="text" name='name' placeholder='Имя'>
                <input type="text" name='patronymic' placeholder='Отчество'>
                <button class='blue_btn' type='submit'>Найти</button>
            </form>
        </div>
        <img class='img_light_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        <img class='img_black_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1_black.svg')}}" alt="">        
    </div>
</section>

<section class="price_service">
    <div class="container grid_two_page">
        <div class="">
            <div class="ul_services">
                @if(isset($products))
                    @if($products->total()>0)
                        <div class="title_our_works">Найдено {{ $products->total() }} захоронения</div>
                        @foreach ($products as $product)
                            <div class="li_product">
                                <div class="one_block_li_product">
                                    @if($product->urlImg()=='default')
                                        <a href='{{ $product->route() }}' ><img class='white_img_org' src="{{$product->defaultImg()[0]}}" alt=""></a>
                                        <a href='{{ $product->route() }}' ><img class='black_img_org' src="{{$product->defaultImg()[1]}}" alt=""></a>
                                    @else
                                        <a href='{{ $product->route() }}' ><img src="{{$product->urlImg() }}" alt=""></a>
                                    @endif
                                    <div class="btn_gray">{{ $product->who }}</div>
                                </div>
                                <div class="two_block_li_product">
                                    <a href='{{ $product->route() }}' class="text_middle_index decoration_on">{{ $product->surname }} {{ $product->name }} {{ $product->patronymic }}</a>
                                    <div class="mini_flex_li_product">
                                        <div class="title_label">Даты захоронения:</div>
                                        <div class="text_li">{{ $product->date_birth }}-{{ $product->date_death }}</div>
                                    </div>
                                    <div class="mini_flex_li_product">
                                        <div class="title_label">Место захоронения:</div>
                                        <div class="text_li">{{ $product->location_death }}</div>
                                    </div>

                                    <div class="flex_btn_li_product">
                                         @if($product->cemetery->price_burial_location==0 || $product->cemetery->price_burial_location==null || $product->userHave())
                                            <div adres='{{ $product->width }},{{ $product->longitude }}'class="blue_btn copy_adres">Скопировать</div>
                                        @else
                                            @auth
                                                <form action="{{ route('order.burial.add.pay',$product->id) }}" method="post" class="get-coordinates-form">
                                                    @csrf
                                                    <button type="submit" class="blue_btn">Получить координаты</button>
                                                </form>
                                            @else
                                                <button type="button" class="blue_btn get-coordinates-btn" 
                                                        data-burial-id="{{ $product->id }}" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#authCoordinatesModal">
                                                    Получить координаты
                                                </button>
                                            @endauth
                                        @endif
                                        <a href='{{ $product->route() }}'class="btn_border_blue">Подробнее</a>
                                        <a href='{{ route('favorite.add',$product->id) }}'class="btn_border_blue img_mini_star"><img src="{{ asset('storage/uploads/Star 1 (1).svg')}}" alt=""></a>
                                    </div>
                                    
                                </div>
                            </div>
                        @endforeach
                        {{ $products->withPath(route('search.burial.result'))->appends($_GET)->links() }}

                   @else
    <div class="block_no_search">
       <div class="title">
    Результаты поиска по запросу:
    @if(isset($searchQuery))
        <span class="search-query-title">
            {{ $searchQuery['surname'] ?? '' }}
            {{ $searchQuery['name'] ?? '' }}
            {{ $searchQuery['patronymic'] ?? '' }}
            @if(isset($searchQuery['date_birth']) && !empty($searchQuery['date_birth']))
                ({{ date('d.m.Y', strtotime($searchQuery['date_birth'])) }})
            @endif
        </span>
    @endif
</div>
        
        <div class="text_li">По вашему запросу ничего не найдено. Проверьте корректность введённых данных или попробуйте расширить критерии поиска, например, убрав значения из некоторых полей.</div>
        
        <div class="block_content_no_search bac_gray">
            <div class="text_no_search">Если Вы не смогли найти в нашей базе интересующее Вас захоронение, Вы можете оставить заявку на его поиск</div>    
            <a href='{{ route('page.search.burial.request') }}' class="blue_btn">Найти захоронение</a>
        </div>    
    </div>    
@endif
                @endif
            </div>
        </div>
        <div class="sidebar">
            <div class="btn_border_blue"  data-bs-toggle="modal" data-bs-target="#beautification_form"><img src="{{asset('storage/uploads/Frame (20).svg')}}" alt="">Облагородить могилу</div>
            <div class="ul_news_sidebar">
                @if (isset($news))
                    @foreach ($news as $news_one )
                        <div class="li_news">
                            <img src="{{asset('storage/'. $news_one->img )}}" alt="">
                            <a href='{{ route('news.single',$news_one->slug) }}' class="title_news">{{ formatContent($news_one->title) }}</a>
                            <div class="text_li">{{ $news_one->created_at->format('d.m.Y') }}</div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="authCoordinatesModal" tabindex="-1" aria-labelledby="authCoordinatesModal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body message">
                <div class="flex_title_message">
                    <div class="text_center">
                        <div class="title_middle">Получить координаты захоронения</div>
                        <div class="text_block">Введите номер телефона для получения доступа</div>
                    </div>
                    <div data-bs-dismiss="modal" class="close_message">
                        <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                    </div>
                </div>
                
                <!-- Шаг 1: Отправка кода -->
                <form class='send_code_form' id="sendCodeForm">
                    <input type="hidden" name="burial_id" id="auth_burial_id" value="">
                    <div class="block_input">
                        <input type="tel" 
                               name="phone" 
                               id="auth_phone" 
                               class="form-control phone-mask" 
                               placeholder="+7 (___) ___-__-__"
                               required>
                        <div class="error-text phone-error" style="display: none;"></div>
                    </div>
                    <div class="blue_btn" id="sendCodeBtn">Отправить код</div>
                </form>
                
                <!-- Шаг 2: Подтверждение кода -->
                <form class='verify_code_form' id="verifyCodeForm" style="display: none;">
                    <div class="flex_btn_error">
                        <div class="block_input">
                            <input type="text" 
                                   name="code" 
                                   id="auth_code" 
                                   placeholder="0000" 
                                   maxlength="6"
                                   required>
                            <div class="error-text code-error" style="display: none;"></div>
                        </div>
                        <div class="blue_btn" id="verifyCodeBtn">Подтвердить</div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="javascript:void(0);" id="backToPhoneBtn">Изменить номер телефона</a>
                    </div>
                </form>
                
                <!-- Шаг 3: Загрузка -->
                <div class="text-center" id="loadingStep" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Загрузка...</span>
                    </div>
                    <p class="mt-3">Перенаправление на оплату...</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/imask"></script>

<script>
$(document).ready(function() {
    // Инициализация маски телефона
    const phoneMask = IMask(
        document.getElementById('auth_phone'),
        {
            mask: '+{7} (000) 000-00-00'
        }
    );

    // Устанавливаем burial_id при открытии модального окна
    $('#authCoordinatesModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const burialId = button.data('burial-id');
        
        $('#auth_burial_id').val(burialId);
        
        // Сброс формы
        resetAuthForm();
    });

    // Сброс формы при закрытии
    $('#authCoordinatesModal').on('hidden.bs.modal', function () {
        resetAuthForm();
    });

    // Кнопка отправки кода
    $('#sendCodeBtn').click(function(e) {
        e.preventDefault();
        
        const phone = phoneMask.unmaskedValue;
        const burialId = $('#auth_burial_id').val();
        
        // Валидация телефона
        if (!phone || phone.length !== 11) {
            showError('.phone-error', 'Введите корректный номер телефона');
            return;
        }
        
        // Очистка ошибок
        hideErrors();
        // Отправка запроса
        $.ajax({
            url: '{{ route("auth.send-code") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                phone: phone,
                burial_id: burialId
            },
            beforeSend: function() {
                $('#sendCodeBtn').prop('disabled', true).text('Отправка...');
            },
            success: function(response) {
                if (response.success) {
                    // Переход к шагу 2
                    $('#sendCodeForm').hide();
                    $('#verifyCodeForm').show();
                    $('#auth_code').focus();
                    
                    // Для тестирования
                    if (response.test_code) {
                        console.log('Тестовый код:', response.test_code);
                    }
                } else {
                    showError('.phone-error', response.message || 'Ошибка отправки кода');
                }
                $('#sendCodeBtn').prop('disabled', false).text('Отправить код');
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.error || xhr.responseJSON?.message || 'Ошибка сервера';
                showError('.phone-error', error);
                $('#sendCodeBtn').prop('disabled', false).text('Отправить код');
            }
        });
    });

    // Кнопка подтверждения кода
    $('#verifyCodeBtn').click(function(e) {
        e.preventDefault();
        const code = $('#auth_code').val();
        const phone = phoneMask.unmaskedValue;
        const burialId = $('#auth_burial_id').val();
        
        // Валидация кода
        if (!code || code.length < 4) {
            showError('.code-error', 'Введите код из SMS');
            return;
        }
        
        hideErrors();
        
        // Показ загрузки
        $('#verifyCodeForm').hide();
        $('#loadingStep').show();
        
        // Отправка запроса
        $.ajax({
            url: '{{ route("auth.verify-code") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                phone: phone,
                code: code,
                burial_id: burialId
            },
            success: function(response) {
                if (response.success) {
                    if (response.payment_url) {
                        // Редирект на оплату
                        window.location.href = response.payment_url;
                    } else if (response.redirect) {
                        // Редирект на страницу захоронения
                        window.location.href = response.redirect;
                    }
                } else {
                    $('#loadingStep').hide();
                    $('#verifyCodeForm').show();
                    showError('.code-error', response.message || 'Ошибка авторизации');
                }
            },
            error: function(xhr) {
                $('#loadingStep').hide();
                $('#verifyCodeForm').show();
                
                let errorMessage = 'Ошибка сервера';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                
                showError('.code-error', errorMessage);
            }
        });
    });

    // Кнопка "Изменить номер телефона"
    $('#backToPhoneBtn').click(function() {
        $('#verifyCodeForm').hide();
        $('#sendCodeForm').show();
        $('.code-error').hide();
        $('#auth_code').val('');
    });

    // Вспомогательные функции
    function showError(selector, message) {
        $(selector).text(message).show();
    }
    
    function hideErrors() {
        $('.error-text').hide();
    }
    
    function resetAuthForm() {
        phoneMask.updateValue('');
        $('#auth_code').val('');
        $('#sendCodeForm').show();
        $('#verifyCodeForm').hide();
        $('#loadingStep').hide();
        hideErrors();
        $('#sendCodeBtn').prop('disabled', false).text('Отправить код');
        $('#verifyCodeBtn').prop('disabled', false).text('Подтвердить');
    }

    // Обработка нажатия Enter в формах
    $('#auth_phone').keypress(function(e) {
        if (e.which === 13) {
            $('#sendCodeBtn').click();
            e.preventDefault();
        }
    });
    
    $('#auth_code').keypress(function(e) {
        if (e.which === 13) {
            $('#verifyCodeBtn').click();
            e.preventDefault();
        }
    });

    // Копирование координат
    $('.copy_adres').click(function() {
        const adres = $(this).attr('adres');
        navigator.clipboard.writeText(adres).then(function() {
            alert('Координаты скопированы!');
        });
    });
});
</script>
@include('footer.footer')