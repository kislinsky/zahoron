@props([
    'placeholder' => 'Поиск организаций...',
    'containerClass' => '',
    'inputClass' => '',
    'apiUrl' => route('organizations.search'),
])

<div class="organization-search-container {{ $containerClass }}" id="organizationSearch">
    {{-- Поисковой инпут --}}
    <div class="search-input-wrapper">
        <div class="search-input-inner">
            <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            
            <input
                type="text"
                id="organizationSearchInput"
                placeholder="{{ $placeholder }}"
                class="search-input {{ $inputClass }}"
                autocomplete="off"
                data-url="{{ $apiUrl }}"
            >
            
            <button 
                type="button"
                id="clearSearchButton"
                class="clear-button"
                style="display: none;"
                aria-label="Очистить поиск"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
            
            <button 
                type="button"
                id="loadingButton"
                class="loading-button"
                style="display: none;"
                disabled
                aria-label="Загрузка..."
            >
                <div class="spinner-input"></div>
            </button>
        </div>
    </div>

    {{-- Контейнер для результатов --}}
    <div id="searchResultsContainer" class="search-results-container" style="display: none;">
        {{-- Заголовок результатов --}}
        <div class="results-header">
            <span class="results-title">Организации</span>
            <span id="resultsCount" class="results-count" style="display: none;"></span>
        </div>

        {{-- Сообщение "Не найдено" --}}
        <div id="noResultsMessage" class="no-results" style="display: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#c1c1c1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                <line x1="8" y1="8" x2="19" y2="19" stroke="#0079D9"></line>
            </svg>
            <span>Ничего не найдено по запросу "<span id="queryText" class="query-text"></span>"</span>
        </div>

        {{-- Список организаций --}}
        <div id="organizationsList" class="organizations-list"></div>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const searchContainer = $('#organizationSearch');
    const searchInput = $('#organizationSearchInput');
    const clearButton = $('#clearSearchButton');
    const loadingButton = $('#loadingButton');
    const resultsContainer = $('#searchResultsContainer');
    const organizationsList = $('#organizationsList');
    const noResultsMessage = $('#noResultsMessage');
    const resultsCount = $('#resultsCount');
    const queryText = $('#queryText');
    
    let searchTimeout = null;
    let currentRequest = null;
    
    // Показывать/скрывать кнопку очистки
    searchInput.on('input', function() {
        if ($(this).val().length > 0) {
            clearButton.show();
        } else {
            clearButton.hide();
            hideResults();
        }
    });
    
    // Очистка поиска
    clearButton.on('click', function() {
        searchInput.val('').focus();
        clearButton.hide();
        hideResults();
    });
    
    // Поиск при вводе
    searchInput.on('input', function() {
        const query = $(this).val().trim();
        
        // Отменяем предыдущий таймаут
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
        
        // Отменяем текущий запрос если есть
        if (currentRequest && currentRequest.readyState !== 4) {
            currentRequest.abort();
        }
        
        if (query.length >= 2) {
            // Запускаем новый таймаут
            searchTimeout = setTimeout(() => {
                performSearch(query);
            }, 300);
        } else if (query.length === 0) {
            hideResults();
        }
    });
    
    // Закрытие результатов при клике вне области
    $(document).on('click', function(e) {
        if (!searchContainer.is(e.target) && searchContainer.has(e.target).length === 0) {
            hideResults();
        }
    });
    
    // Закрытие по ESC
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            hideResults();
            searchInput.blur();
        }
    });
    
    // Функция поиска
   // В файле organization-search.js обновите функцию performSearch:

function performSearch(query) {
    showLoading();
    showResults();
    
    const apiUrl = searchInput.data('url');
    
    // Добавляем CSRF токен для Laravel
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    currentRequest = $.ajax({
        url: apiUrl,
        method: 'GET',
        data: { 
            query: query,
            _token: $('meta[name="csrf-token"]').attr('content') // для безопасности
        },
        dataType: 'json',
        success: function(response) {
            console.log('Search response:', response); // Для отладки
            
            if (response.success) {
                if (response.data.length === 0) {
                    // Показываем сообщение, что в этом городе не найдено
                    showNoResultsInCity(query, response.city_id);
                } else {
                    displayResults(response.data, query);
                }
            } else {
                showErrorMessage(response.message || 'Ошибка поиска');
            }
        },
        error: function(xhr, status, error) {
            if (status !== 'abort') {
                showErrorMessage('Ошибка соединения с сервером');
            }
        },
        complete: function() {
            hideLoading();
            currentRequest = null;
        }
    });
}

// Новая функция для показа сообщения "не найдено в городе"
function showNoResultsInCity(query, cityId) {
    organizationsList.hide().empty();
    resultsCount.hide();
    queryText.text(query);
    
    // Изменяем сообщение
    noResultsMessage.html(`
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#c1c1c1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <div>
            <div>По запросу "<span class="query-text">${query}</span>" ничего не найдено</div>
            <div class="city-notice" style="font-size: 12px; margin-top: 5px; color: #0079D9;">
                Поиск выполняется только в текущем городе
            </div>
        </div>
    `);
    noResultsMessage.show();
}

function showErrorMessage(message) {
    organizationsList.hide().empty();
    resultsCount.hide();
    queryText.text('');
    
    noResultsMessage.html(`
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#c1c1c1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12" y2="16"></line>
        </svg>
        <span>${message}</span>
    `);
    noResultsMessage.show();
}
    
    // Отображение результатов
    function displayResults(organizations, query) {
        organizationsList.empty();
        
        if (organizations.length > 0) {
            noResultsMessage.hide();
            
            // Показываем количество
            resultsCount.text(organizations.length + ' найдено').show();
            
            // Добавляем организации
            organizations.forEach(function(org) {
                const orgElement = createOrganizationElement(org);
                organizationsList.append(orgElement);
            });
            
            organizationsList.show();
        } else {
            showNoResults(query);
        }
    }
    
    // Создание элемента организации
    function createOrganizationElement(org) {
        // Определяем статус
        const status = org.open_or_not || org.status || 'Неизвестно';
        const isOpen = status === 'Открыто';
        
        // Формируем звезды рейтинга
        const rating = Math.round(org.rating || 0);
        let starsHtml = '';
        
        for (let i = 1; i <= 5; i++) {
            const starClass = i <= rating ? 'star-filled' : 'star-empty';
            starsHtml += `
                <svg class="${starClass}" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
            `;
        }
        
        return `
            <a href="${org.route || '#'}" class="organization-card">
                <div class="org-image-container">
                    <img src="${org.image_url || '/images/default-organization.jpg'}" 
                         alt="${org.name}" 
                         class="org-image"
                         onerror="this.src='/images/default-organization.jpg'">
                </div>
                <div class="org-info">
                    <h3 class="org-name" title="${org.name}">${org.name}</h3>
                    ${org.rating > 0 ? `
                        <div class="org-rating">
                            <div class="stars">${starsHtml}</div>
                            <span class="rating-value">${org.rating.toFixed(1)}</span>
                        </div>
                    ` : ''}
                    ${org.address ? `<div class="org-address" title="${org.address}">${org.address}</div>` : ''}
                    <div class="org-meta">
                        <span class="org-status ${isOpen ? 'status-open' : 'status-closed'}">${status}</span>
                        ${org.reviews_count ? `<span>${org.reviews_count} отзывов</span>` : ''}
                    </div>
                </div>
            </a>
        `;
    }
    
    // Показать "не найдено"
    function showNoResults(query) {
        organizationsList.hide().empty();
        resultsCount.hide();
        queryText.text(query);
        noResultsMessage.show();
    }
    
    // Управление отображением
    function showLoading() {
        loadingButton.show();
        clearButton.hide();
    }
    
    function hideLoading() {
        loadingButton.hide();
        if (searchInput.val().length > 0) {
            clearButton.show();
        }
    }
    
    function showResults() {
        resultsContainer.show();
    }
    
    function hideResults() {
        resultsContainer.hide();
        organizationsList.hide().empty();
        noResultsMessage.hide();
        resultsCount.hide();
    }
    
    // Инициализация
    searchInput.on('focus', function() {
        if ($(this).val().length >= 2 && organizationsList.children().length > 0) {
            showResults();
        }
    });
});
</script>
