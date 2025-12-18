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

        <link rel="icon" type="image/x-icon" href="{{ asset('storage/uploads/favicon.ico') }}">

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
                    <a href='{{route('marketplace.category','organizacia-pohoron')}}'class="btn_bac_gray">Ритуальные товары, услуги </a>
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

@if(env('AI_CHAT')!='off')
 <div class="chat-widget">
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
    </div> 
@endif
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

    // Конфигурация
    const CHAT_CONFIG = {
        MAX_MESSAGES: 100,
        MESSAGE_TTL: 30 * 24 * 60 * 60 * 1000,
        STORAGE_KEY: 'chat_data',
        CHAT_ID_KEY: 'chat_id',
        ALLOWED_HTML_TAGS: ['b', 'strong', 'i', 'em', 'u', 'br', 'p', 'div', 'span', 'code', 'pre', 'ul', 'ol', 'li', 'a']
    };

    // Функция для безопасного отображения HTML
    function safeHtml(html) {
        if (!html) return '';
        
        // Создаем временный элемент для парсинга HTML
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        
        // Функция для рекурсивной очистки элементов
        function sanitizeNode(node) {
            if (node.nodeType === Node.TEXT_NODE) {
                return node.textContent;
            }
            
            if (node.nodeType === Node.ELEMENT_NODE) {
                const tagName = node.tagName.toLowerCase();
                
                // Разрешаем только определенные теги
                if (CHAT_CONFIG.ALLOWED_HTML_TAGS.includes(tagName)) {
                    const allowedElement = document.createElement(tagName);
                    
                    // Копируем атрибуты (только безопасные)
                    for (let attr of node.attributes) {
                        if (tagName === 'a' && attr.name === 'href') {
                            // Для ссылок проверяем протокол
                            if (attr.value.startsWith('http://') || 
                                attr.value.startsWith('https://') ||
                                attr.value.startsWith('/') ||
                                attr.value.startsWith('#') ||
                                attr.value.startsWith('mailto:')) {
                                allowedElement.setAttribute(attr.name, attr.value);
                            }
                        } else if (['class', 'style', 'id'].includes(attr.name)) {
                            allowedElement.setAttribute(attr.name, attr.value);
                        }
                    }
                    
                    // Рекурсивно обрабатываем дочерние элементы
                    for (let child of node.childNodes) {
                        const sanitizedChild = sanitizeNode(child);
                        if (sanitizedChild) {
                            if (typeof sanitizedChild === 'string') {
                                allowedElement.appendChild(document.createTextNode(sanitizedChild));
                            } else {
                                allowedElement.appendChild(sanitizedChild);
                            }
                        }
                    }
                    
                    return allowedElement;
                } else {
                    // Для запрещенных тегов оставляем только текст
                    let textContent = '';
                    for (let child of node.childNodes) {
                        const sanitized = sanitizeNode(child);
                        if (typeof sanitized === 'string') {
                            textContent += sanitized;
                        }
                    }
                    return textContent;
                }
            }
            
            return '';
        }
        
        // Очищаем HTML
        const sanitizedContent = sanitizeNode(tempDiv);
        
        if (typeof sanitizedContent === 'string') {
            return sanitizedContent;
        } else {
            return sanitizedContent.outerHTML;
        }
    }

    // Функция для определения, содержит ли текст HTML
    function containsHtml(text) {
        if (!text) return false;
        return /<[a-z][\s\S]*>/i.test(text);
    }

    // Функция для расчета высоты чата
    function calculateChatHeight() {
        const isMobile = $(window).width() <= 440;
        
        if (isMobile) {
            const headerHeight = $('.chat-header').outerHeight(true) || 90;
            const inputHeight = $('.chat-input-container').outerHeight(true) || 74;
            const windowHeight = $(window).height();
            const availableHeight = windowHeight - headerHeight - inputHeight;
            
            chatMessages.css({
                'height': availableHeight + 'px',
                'max-height': availableHeight + 'px',
                'overflow-y': 'auto'
            });
        } else {
            chatMessages.css({
                'height': '500px',
                'max-height': '500px',
                'overflow-y': 'auto'
            });
        }
    }

    // Универсальное хранилище
    const storage = {
        set: function(key, value) {
            try {
                if (typeof(Storage) !== "undefined") {
                    localStorage.setItem(key, JSON.stringify(value));
                    return true;
                }
            } catch (e) {
                console.warn('LocalStorage недоступен, используем cookies', e);
                return this.setCookie(key, value, 30);
            }
            return false;
        },

        get: function(key) {
            try {
                if (typeof(Storage) !== "undefined") {
                    const item = localStorage.getItem(key);
                    return item ? JSON.parse(item) : null;
                }
            } catch (e) {
                console.warn('LocalStorage недоступен, читаем из cookies', e);
                return this.getCookie(key);
            }
            return null;
        },

        remove: function(key) {
            try {
                if (typeof(Storage) !== "undefined") {
                    localStorage.removeItem(key);
                    return true;
                }
            } catch (e) {
                return this.setCookie(key, '', -1);
            }
            return false;
        },

        setCookie: function(name, value, days) {
            try {
                const expires = new Date();
                expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
                document.cookie = name + '=' + encodeURIComponent(JSON.stringify(value)) + 
                                 ';expires=' + expires.toUTCString() + 
                                 ';path=/;SameSite=Lax;Secure';
                return true;
            } catch (e) {
                console.error('Ошибка записи cookie:', e);
                return false;
            }
        },

        getCookie: function(name) {
            try {
                const nameEQ = name + '=';
                const ca = document.cookie.split(';');
                for (let i = 0; i < ca.length; i++) {
                    let c = ca[i].trim();
                    if (c.indexOf(nameEQ) === 0) {
                        const value = c.substring(nameEQ.length);
                        return value ? JSON.parse(decodeURIComponent(value)) : null;
                    }
                }
                return null;
            } catch (e) {
                console.error('Ошибка чтения cookie:', e);
                return null;
            }
        }
    };

    // Генерация ID чата
    function generateChatId() {
        return 'chat_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    // Получение или создание ID чата
    function getChatId() {
        let chatId = storage.get(CHAT_CONFIG.CHAT_ID_KEY);
        if (!chatId) {
            chatId = generateChatId();
            storage.set(CHAT_CONFIG.CHAT_ID_KEY, chatId);
        }
        return chatId;
    }

    // Структура данных чата
    function createChatData() {
        return {
            id: getChatId(),
            createdAt: new Date().toISOString(),
            lastActivity: new Date().toISOString(),
            messages: [],
            version: '1.0'
        };
    }

    // Получение данных чата
    function getChatData() {
        let chatData = storage.get(CHAT_CONFIG.STORAGE_KEY);
        
        if (!chatData || !chatData.messages) {
            chatData = createChatData();
            storage.set(CHAT_CONFIG.STORAGE_KEY, chatData);
        }
        
        return cleanupChatData(chatData);
    }

    // Сохранение данных чата
    function saveChatData(chatData) {
        chatData.lastActivity = new Date().toISOString();
        chatData.messageCount = chatData.messages.length;
        
        if (chatData.messages.length > CHAT_CONFIG.MAX_MESSAGES) {
            chatData.messages = chatData.messages.slice(-CHAT_CONFIG.MAX_MESSAGES);
        }
        
        return storage.set(CHAT_CONFIG.STORAGE_KEY, chatData);
    }

    // Очистка устаревших данных
    function cleanupChatData(chatData) {
        const now = Date.now();
        const validMessages = [];
        
        for (const message of chatData.messages) {
            const messageAge = now - new Date(message.timestamp).getTime();
            
            if (messageAge <= CHAT_CONFIG.MESSAGE_TTL) {
                validMessages.push(message);
            }
        }
        
        chatData.messages = validMessages;
        return chatData;
    }

    // Добавление сообщения в историю
    function addMessageToHistory(text, sender) {
        const chatData = getChatData();
        
        const message = {
            id: 'msg_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5),
            text: text,
            sender: sender,
            timestamp: new Date().toISOString(),
            type: 'text',
            containsHtml: containsHtml(text)
        };
        
        chatData.messages.push(message);
        const success = saveChatData(chatData);
        
        return success;
    }

    // Получение истории сообщений
    function getMessageHistory() {
        const chatData = getChatData();
        return chatData.messages || [];
    }

    // Очистка истории чата
    function clearChatHistory() {
        const chatData = createChatData();
        storage.set(CHAT_CONFIG.STORAGE_KEY, chatData);
        storage.remove(CHAT_CONFIG.CHAT_ID_KEY);
        
        return chatData;
    }

    // Загрузка истории в интерфейс
    function loadChatHistory() {
        const messages = getMessageHistory();
        chatMessages.empty();
        
        if (messages.length === 0) {
            addMessageToUI('Здравствуйте! Чем могу помочь?', 'bot', false);
        } else {
            messages.forEach(msg => {
                addMessageToUI(msg.text, msg.sender, false, msg.containsHtml);
            });
        }
        
        scrollToBottom();
    }

    // Добавление сообщения в UI
    function addMessageToUI(text, sender, saveToHistory = true, isHtml = false) {
        const messageElement = $('<div>')
            .addClass('message_ai')
            .addClass(sender === 'user' ? 'message-user' : 'message-bot');
            
        // Если текст содержит HTML и это разрешено, обрабатываем безопасно
        if (isHtml || containsHtml(text)) {
            messageElement.html(safeHtml(text));
        } else {
            messageElement.text(text);
        }
            
        chatMessages.append(messageElement);
        
        if (saveToHistory) {
            addMessageToHistory(text, sender);
        }
        
        scrollToBottom();
    }

    // Прокрутка вниз
    function scrollToBottom() {
        setTimeout(() => {
            if (chatMessages[0] && chatMessages[0].scrollHeight > chatMessages[0].clientHeight) {
                chatMessages.scrollTop(chatMessages[0].scrollHeight);
            }
        }, 100);
    }

    // Отправка сообщения
    function sendMessage() {
        const message = chatInput.val().trim();
        if (message === '') return;

        // Добавляем сообщение пользователя
        addMessageToUI(message, 'user');
        chatInput.val('');
        chatInput.prop('disabled', true);
        sendButton.prop('disabled', true);

        // Индикатор загрузки
        const loadingElement = $('<div>')
            .addClass('message_ai message-bot loading')
            .text('⏳ Отправляем сообщение...');
            
        chatMessages.append(loadingElement);
        scrollToBottom();

        // Отправка на сервер
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
            timeout: 30000,
            success: function(response) {
                loadingElement.remove();
                
                const botResponse = response && response !== '' ? response : 'Не удалось получить ответ от сервера';
                
                // Определяем, содержит ли ответ HTML
                const responseContainsHtml = containsHtml(botResponse);
                addMessageToUI(botResponse, 'bot', true, responseContainsHtml);
                
                enableInput();
            },
            error: function(xhr, status, error) {
                loadingElement.remove();
                
                let errorMessage = 'Ошибка соединения';
                if (status === 'timeout') errorMessage = 'Превышено время ожидания';
                else if (xhr.status === 0) errorMessage = 'Нет соединения с интернетом';
                else errorMessage = 'Ошибка сервера: ' + xhr.status;
                
                addMessageToUI(errorMessage, 'bot');
                enableInput();
            }
        });
    }

    // Активация поля ввода
    function enableInput() {
        chatInput.prop('disabled', false);
        sendButton.prop('disabled', false);
        chatInput.focus();
    }

    // Обработчики событий
    chatToggle.on('click', function() {
        chatContainer.slideToggle(300, function() {
            if ($(this).is(':visible')) {
                calculateChatHeight();
                loadChatHistory();
                chatInput.focus();
            }
        });
    });

    chatClose.on('click', function() {
        chatContainer.slideUp(300);
    });

    sendButton.on('click', sendMessage);

    chatInput.on('keypress', function(e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Обновление высоты при изменении размера окна
    $(window).on('resize', function() {
        if (chatContainer.is(':visible')) {
            calculateChatHeight();
            scrollToBottom();
        }
    });

    // Закрытие по клику вне области
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.chat-widget').length && chatContainer.is(':visible')) {
            chatContainer.slideUp(300);
        }
    });

    // Инициализация
    if (chatContainer.is(':visible')) {
        calculateChatHeight();
        loadChatHistory();
    }
});

</script>
