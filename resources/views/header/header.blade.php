<?php 
$user=user();
$city=selectCity();
use Artesaos\SEOTools\Facades\SEOTools;
?>


<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">


        {!! SEOTools::generate() !!}

        @if(request()->has('page') && request()->get('page') > 1)
            <link rel="canonical" href="{{ url()->current() }}?{{ http_build_query(request()->except('page')) }}">
        @else
            <link rel="canonical" href="{{ url()->current() }}?{{ http_build_query(request()->all()) }}">
        @endif
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

        <!-- Fonts -->
        <link  defer href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
        <link defer rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
        <link  defer rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

        <link defer rel="stylesheet" href="{{asset('css/style.css')}}">
       
        <link defer rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/css/lightgallery-bundle.min.css" />

        <script defer src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/lightgallery.min.js"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/plugins/zoom/lg-zoom.min.js"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/plugins/thumbnail/lg-thumbnail.min.js"></script>
        <script  src="https://api-maps.yandex.ru/2.1/?apikey=373ac95d-ec8d-4dfc-a70c-e48083741c72&lang=ru_RU"></script>

        {!! get_acf(20,'header') !!}
    </head>

<body class='{{getTheme()}}'>
<div class="margin_top_for_header"></div>
@include('components.all-forms-message')

@include('header.header-mobile')


<div class="bac_black city_question">
    <div class='message'>
        <div class="flex_title_message">
            <div class="title_middle">Выберите город</div>
            <div class="close_message">
                <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
            </div>
        </div>
        <div class="content_message">{!!  session("message_words_memory") !!}  </div>
        <div class="flex_btn">
            <div class="blue_btn close_message">Оставить по умолчанию</div>
            <div class="blue_btn close_message open_choose_city">Выбрать город</div>
        </div>
    </div>
</div>


<header class='header_main'>
   
    <div class="container">  
        <a class='logo' href='{{route('index')}}'>
            <img class='img_light_theme' src='{{asset('storage/uploads/zahoron.svg')}}'>
            <img class='img_black_theme' src="{{asset('storage/uploads/РИТУАЛреестр.svg')}}" alt="">
        </a>
        @if (isset($page))
            <?php $page=$page;?>
        @else
            <?php $page=null;?>
        @endif
        <div class='pages'>
            <div id_city_selected='{{ $city->id }}' class="btn_bac_gray city_selected">
                <img class='img_light_theme'src='{{ asset('storage/uploads/Group (22).svg') }}'>
                <img class='img_black_theme'src='{{ asset('storage/uploads/Group_black_theme.svg') }}'>
                {{ $city->title }}
            </div>

            <div class="btn_bac_gray open_children_pages">
               Ритуальные услуги
                <div class='children_pages'>
                    <a href='{{ route('organizations.category','organizacia-pohoron') }}'class="btn_bac_gray">Ритуальные агенства </a>
                    <a href=''class="btn_bac_gray">Ритуальные товары, услуги </a>
                </div>
                <img class='img_light_theme' src='{{asset('storage/uploads/Vector 9 (1).svg')}}'>
                <img class='img_black_theme' src='{{asset('storage/uploads/Vector 9_black.svg')}}'>
            </div>

            <div class="btn_bac_gray open_children_pages">
               Ритуальные обьекты
                <div class='children_pages'>
                    <a href='{{ route('cemeteries') }}'class="btn_bac_gray">Кладбища </a>
                    <a href='{{ route('mortuaries') }}'class="btn_bac_gray">Морги </a>
                   
                </div>
                <img class='img_light_theme' src='{{asset('storage/uploads/Vector 9 (1).svg')}}'>
                <img class='img_black_theme' src='{{asset('storage/uploads/Vector 9_black.svg')}}'>
            </div>


            <div class="btn_bac_gray open_children_pages">
                Поиск могил
                <div class='children_pages'>
                    <a href='{{ route('search.burial') }}'class="btn_bac_gray <?php if($page==11){echo ' active_label_product';}?>">Поиск </a>
                    <a href='{{ route('page.search.burial.filter') }}'class="btn_bac_gray <?php if($page==1){echo ' active_label_product';}?>">Герои </a>
                    <a href='{{ route('page.search.burial.request') }}'class="btn_bac_gray <?php if($page==3){echo ' active_label_product';}?>">Заявка на поиск</a>
                </div>
                <img class='img_light_theme' src='{{asset('storage/uploads/Vector 9 (1).svg')}}'>
                <img class='img_black_theme' src='{{asset('storage/uploads/Vector 9_black.svg')}}'>
 
            </div>
           

            <div class="btn_bac_gray open_children_pages">
               Информация
                <div class='children_pages'>
                    <a href='{{ route('our.products') }}'class="btn_bac_gray">Наши работы </a>
                    <a href='{{ route('news') }}'class="btn_bac_gray">Статьи </a>
                    <a href='{{ route('contacts') }}'class="btn_bac_gray">Контакты </a>
                   
                </div>
                <img class='img_light_theme' src='{{asset('storage/uploads/Vector 9 (1).svg')}}'>
                <img class='img_black_theme' src='{{asset('storage/uploads/Vector 9_black.svg')}}'>
            </div>
           
        </div> 
       
        
        <div class='flex_icon_header'>
           <div class='change_theme icon_header'><img class='img_light_theme' src='{{asset('storage/uploads/Group 23.svg')}}'><img class='img_black_theme' src='{{asset('storage/uploads/Group 23_black_theme.svg')}}'></div>
            <a href='{{ route('login') }}' class='icon_header icon_login'><img class='img_black_theme' src='{{asset('storage/uploads/Group 1_black_theme.svg')}}'><img class='img_light_theme' src='{{asset('storage/uploads/Group 1 (2).svg')}}'></a>
        </div>
    </div>
</header>


<div class="mobile_header_mini_info">
    @if(versionProject())
        <div class="btn_border_blue">
            <img src="{{asset('storage/uploads/Frame (3).svg')}}" alt="">
            Облагородить 
        </div>
    @else
        <div data-bs-toggle="modal" data-bs-target="#beautification_form" class="btn_border_blue">
            <img src="{{asset('storage/uploads/Frame (3).svg')}}" alt="">
            Облагородить 
        </div>
    @endif
    
    <div class="flex_icon_header">
        <a href='{{ route('login') }}' class='icon_header icon_login'><img class='img_black_theme' src='{{asset('storage/uploads/Group 1_black_theme.svg')}}'><img class='img_light_theme' src='{{asset('storage/uploads/Group 1 (2).svg')}}'></a>
        <div class='gray_circle icon_header open_mobile_header' >
            <img class='img_light_theme'src="{{asset('storage/uploads/Group 29.svg')}}" alt="">
            <img class='img_black_theme'src="{{asset('storage/uploads/Group 29 (1)_black.svg')}}" alt="">
        </div>
    </div>
</div>
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>


  {{-- <div class="chat-widget">
        <button class="chat-button" id="chatToggle">
            <svg viewBox="0 0 24 24">
                <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
            </svg>
        </button>
        
        <div class="chat-container" id="chatContainer">
            <div class="chat-header">
                <div class="chat-avatar">Z</div>
                <div class="chat-info">
                    <h3>Zahoron.ru</h3>
                    <p>Онлайн-консультант</p>
                </div>
                <button class="chat-close" id="chatClose">
                    <svg viewBox="0 0 24 24" width="20" height="20">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                </button>
            </div>
            
            <div class="chat-messages" id="chatMessages">
                <div class="message message-bot">
                    Здравствуйте! Я консультант Zahoron.ru. Чем могу помочь?
                </div>
            </div>
            
            <div class="typing-indicator" id="typingIndicator">
                <div class="typing-dots">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            </div>
            
            <div class="chat-input-container">
                <input type="text" class="chat-input" id="chatInput" placeholder="Введите ваше сообщение...">
                <button class="send-button" id="sendButton">
                    <svg viewBox="0 0 24 24">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div> --}}

<script>

$( ".change_theme" ).on( "click", function() {
    $('body').toggleClass('black_theme')

    $.get("{{route('change-theme')}}", function (response) {
    });
})



$(document).ready(function() {
    const chatToggle = $('#chatToggle');
    const chatContainer = $('#chatContainer');
    const chatClose = $('#chatClose');
    const chatMessages = $('#chatMessages');
    const chatInput = $('#chatInput');
    const sendButton = $('#sendButton');
    const typingIndicator = $('#typingIndicator');

    // Функции для работы с куками
    function setCookie(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
        document.cookie = name + '=' + encodeURIComponent(value) + ';expires=' + expires.toUTCString() + ';path=/;SameSite=Lax';
    }

    function getCookie(name) {
        const nameEQ = name + '=';
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
        }
        return null;
    }

    // Генерация или получение ID чата
    function getChatId() {
        let chatId = getCookie('chat_id');
        if (!chatId) {
            chatId = 'chat_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            setCookie('chat_id', chatId, 30);
        }
        return chatId;
    }

    // Сохранение сообщений в куки
    function saveMessageToCookie(message, sender) {
        try {
            const messages = getMessagesFromCookie();
            messages.push({
                text: message,
                sender: sender,
                timestamp: new Date().toISOString()
            });
            
            // Сохраняем только последние 50 сообщений
            const recentMessages = messages.slice(-50);
            setCookie('chat_messages', JSON.stringify(recentMessages), 30);
        } catch (error) {
            console.error('Ошибка сохранения в куки:', error);
        }
    }

    // Получение сообщений из куки
    function getMessagesFromCookie() {
        try {
            const messagesCookie = getCookie('chat_messages');
            return messagesCookie ? JSON.parse(messagesCookie) : [];
        } catch (error) {
            console.error('Ошибка чтения куки:', error);
            return [];
        }
    }

    // Загрузка истории сообщений при открытии чата
    function loadChatHistory() {
        try {
            const messages = getMessagesFromCookie();
            chatMessages.empty();
            
            if (messages.length === 0) {
                // Если сообщений нет, показываем приветствие
                addMessage('Здравствуйте! Чем могу помочь?', 'bot', false);
            } else {
                // Загружаем все сообщения из истории
                messages.forEach(msg => {
                    addMessage(msg.text, msg.sender, false);
                });
            }
            // Прокручиваем вниз после загрузки
            setTimeout(() => {
                chatMessages.scrollTop(chatMessages[0].scrollHeight);
            }, 100);
        } catch (error) {
            console.error('Ошибка загрузки истории:', error);
        }
    }

    // Открытие/закрытие чата
    chatToggle.on('click', function() {
        chatContainer.slideToggle(300, function() {
            if ($(this).is(':visible')) {
                loadChatHistory();
                chatInput.focus();
            }
        });
    });

    chatClose.on('click', function() {
        chatContainer.slideUp(300);
    });

    // Добавление сообщения в чат
    function addMessage(text, sender, saveToCookie = true) {
        const messageElement = $('<div>').addClass('message_ai').addClass(`message-${sender}`).text(text);
        chatMessages.append(messageElement);
        
        // Прокручиваем к новому сообщению
        setTimeout(() => {
            chatMessages.scrollTop(chatMessages[0].scrollHeight);
        }, 100);

        // Сохраняем в куки (кроме случаев когда загружаем историю)
        if (saveToCookie) {
            saveMessageToCookie(text, sender);
        }
    }

    // Отправка сообщения
    function sendMessage() {
        const message = chatInput.val().trim();
        if (message === '') return;

        // Добавляем сообщение пользователя и сразу сохраняем в куки
        addMessage(message, 'user');
        chatInput.val('');
        chatInput.prop('disabled', true);
        sendButton.prop('disabled', true);

        // НЕ добавляем сообщение "Отправка..." - вместо этого показываем индикатор
        const loadingElement = $('<div>').addClass('message').addClass('message-bot loading').text('⏳ Отправляем сообщение...');
        chatMessages.append(loadingElement);
        chatMessages.scrollTop(chatMessages[0].scrollHeight);

        // Отправляем на сервер
        sendToAI(message, loadingElement);
    }

    // Отправка на AI API
    function sendToAI(userMessage, loadingElement) {
        const chatId = getChatId();
        
        $.ajax({
            url: '{{ route("ai-message.send") }}',
            type: 'GET',
            data: {
                message_ai: userMessage,
                chat_id: chatId
            },
            success: function(response) {
                // Убираем индикатор загрузки
                loadingElement.remove();
                
                // Добавляем реальный ответ бота и сохраняем в куки
                let botResponse = 'Не удалось получить ответ от сервера';
                
                if (response && response !== '') {
                    botResponse = response;
                }
                
                addMessage(botResponse, 'bot');
                
                chatInput.prop('disabled', false);
                sendButton.prop('disabled', false);
                chatInput.focus();
            },
            error: function(xhr, status, error) {
                // Убираем индикатор загрузки
                loadingElement.remove();
                
                // Добавляем сообщение об ошибке и сохраняем в куки
                let errorMessage = 'Ошибка сервера';
                
                if (status === 'timeout') {
                    errorMessage = 'Превышено время ожидания ответа';
                } else if (xhr.status === 0) {
                    errorMessage = 'Нет соединения с интернетом';
                } else {
                    errorMessage = 'Ошибка сервера: ' + xhr.status;
                }
                
                addMessage(errorMessage, 'bot');
                
                chatInput.prop('disabled', false);
                sendButton.prop('disabled', false);
                chatInput.focus();
            }
        });
    }

    // Обработчики событий
    sendButton.on('click', sendMessage);
    
    chatInput.on('keypress', function(e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Закрытие чата при клике вне области
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.chat-widget').length && chatContainer.is(':visible')) {
            chatContainer.slideUp(300);
        }
    });

    // Автоинициализация
    console.log('Чат виджет инициализирован');
});
</script>