{{-- resources/views/components/product-search.blade.php --}}
@props([
    'placeholder' => 'Поиск товаров...',
    'containerClass' => '',
    'inputClass' => '',
    'apiUrl' => route('products.search'),
    'organizationId' => null, // опционально: поиск только в конкретной организации
])

<div class="product-search-container {{ $containerClass }}" id="productSearch">
    {{-- Поисковой инпут --}}
    <div class="search-input-wrapper">
        <div class="search-input-inner">
            <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            
            <input
                type="text"
                id="productSearchInput"
                placeholder="{{ $placeholder }}"
                class="search-input {{ $inputClass }}"
                autocomplete="off"
                data-url="{{ $apiUrl }}"
                @if($organizationId) data-organization-id="{{ $organizationId }}" @endif
            >
            
            <button 
                type="button"
                id="clearProductSearchButton"
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
                id="loadingProductButton"
                class="loading-button"
                style="display: none;"
                disabled
                aria-label="Загрузка..."
            >
                <div class="spinner"></div>
            </button>
        </div>
    </div>

    {{-- Контейнер для результатов --}}
    <div id="productSearchResultsContainer" class="search-results-container" style="display: none;">
        {{-- Заголовок результатов --}}
        <div class="results-header">
            <span class="results-title">Товары</span>
            <span id="productResultsCount" class="results-count" style="display: none;"></span>
        </div>

        {{-- Сообщение "Не найдено" --}}
        <div id="productNoResultsMessage" class="no-results" style="display: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#c1c1c1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                <line x1="8" y1="8" x2="19" y2="19" stroke="#0079D9"></line>
            </svg>
            <span>Ничего не найдено по запросу "<span id="productQueryText" class="query-text"></span>"</span>
        </div>

        {{-- Список товаров --}}
        <div id="productsList" class="products-list"></div>
    </div>
</div>

<style>
/* Стили для поиска товаров */
.product-search-container {
    position: relative;
    max-width: 600px;
    margin: 0 auto;
}

.search-input-wrapper {
    position: relative;
}

.search-input-inner {
    position: relative;
    display: flex;
    align-items: center;
}

.search-input {
    width: 100%;
    padding: 12px 45px 12px 45px;
    font-size: 16px;
    border: 2px solid #c1c1c1;
    border-radius: 8px;
    background: white;
    transition: all 0.3s ease;
    outline: none;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.search-input:focus {
    border-color: #0079D9;
    box-shadow: 0 0 0 3px rgba(0, 121, 217, 0.1);
}

.search-icon {
    position: absolute;
    left: 15px;
    color: #c1c1c1;
    pointer-events: none;
}

.clear-button, .loading-button {
    position: absolute;
    right: 15px;
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    color: #c1c1c1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.clear-button:hover {
    color: #666;
}

.loading-button {
    cursor: default;
}

.spinner {
    width: 20px;
    height: 20px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #0079D9;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Результаты поиска */
.search-results-container {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    margin-top: 8px;
    z-index: 1000;
    max-height: 500px;
    overflow-y: auto;
    border: 1px solid #e1e1e1;
}

.results-header {
    padding: 12px 16px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.results-title {
    font-weight: 600;
    color: #333;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.results-count {
    font-size: 12px;
    color: #0079D9;
    background: rgba(0, 121, 217, 0.1);
    padding: 4px 8px;
    border-radius: 12px;
}

.no-results {
    padding: 20px;
    text-align: center;
    color: #666;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
}

.query-text {
    font-weight: 600;
    color: #333;
}

.products-list {
    padding: 8px 0;
}

/* Карточка товара */
.product-card {
    display: flex;
    padding: 12px 16px;
    text-decoration: none;
    color: inherit;
    transition: background-color 0.2s;
    border-bottom: 1px solid #f5f5f5;
    align-items: flex-start;
}

.product-card:hover {
    background-color: #f8f9fa;
}

.product-card:last-child {
    border-bottom: none;
}

.product-image-container {
    width: 80px;
    height: 80px;
    flex-shrink: 0;
    border-radius: 6px;
    overflow: hidden;
    margin-right: 12px;
    background: #f5f5f5;
    position: relative;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-info {
    flex: 1;
    min-width: 0;
}

.product-name {
    margin: 0 0 8px 0;
    font-size: 15px;
    font-weight: 600;
    color: #333;
    line-height: 1.4;
}

.product-price {
    font-size: 16px;
    font-weight: 700;
    color: #0079D9;
    margin-bottom: 4px;
}

.product-old-price {
    font-size: 13px;
    color: #999;
    text-decoration: line-through;
    margin-right: 8px;
}

.product-rating {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-bottom: 4px;
}

.stars {
    display: flex;
    gap: 2px;
}

.star-filled {
    color: #FFB800;
}

.star-empty {
    color: #e0e0e0;
}

.rating-value {
    font-size: 12px;
    color: #666;
    margin-left: 4px;
}

.product-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #888;
    margin-top: 6px;
}

.product-category {
    background: rgba(0, 121, 217, 0.1);
    color: #0079D9;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 500;
}

.product-organization {
    font-size: 13px;
    color: #666;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Бейдж акции/скидки */
.product-badge {
    position: absolute;
    top: 6px;
    left: 6px;
    background: #FF4757;
    color: white;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 4px;
    z-index: 1;
}

/* Анимация появления */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.search-results-container[style*="display: block"] {
    animation: fadeInUp 0.2s ease-out;
}
</style>

<script>
$(document).ready(function() {
    const searchContainer = $('#productSearch');
    const searchInput = $('#productSearchInput');
    const clearButton = $('#clearProductSearchButton');
    const loadingButton = $('#loadingProductButton');
    const resultsContainer = $('#productSearchResultsContainer');
    const productsList = $('#productsList');
    const noResultsMessage = $('#productNoResultsMessage');
    const resultsCount = $('#productResultsCount');
    const queryText = $('#productQueryText');
    
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
    function performSearch(query) {
        showLoading();
        showResults();
        
        const apiUrl = searchInput.data('url');
        const organizationId = searchInput.data('organization-id');
        
        // Добавляем CSRF токен для Laravel
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        const requestData = {
            query: query,
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        // Добавляем ID организации если указан
        if (organizationId) {
            requestData.organization_id = organizationId;
        }
        
        currentRequest = $.ajax({
            url: apiUrl,
            method: 'GET',
            data: requestData,
            dataType: 'json',
            success: function(response) {
                console.log('Product search response:', response);
                
                if (response.success) {
                    if (response.data.length === 0) {
                        showNoResults(query);
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
    
    // Отображение результатов
    function displayResults(products, query) {
        productsList.empty();
        
        if (products.length > 0) {
            noResultsMessage.hide();
            
            // Показываем количество
            resultsCount.text(products.length + ' найдено').show();
            
            // Добавляем товары
            products.forEach(function(product) {
                const productElement = createProductElement(product);
                productsList.append(productElement);
            });
            
            productsList.show();
        } else {
            showNoResults(query);
        }
    }
    
    // Создание элемента товара
    function createProductElement(product) {
        // Формируем звезды рейтинга
        const rating = Math.round(product.rating || 0);
        let starsHtml = '';
        
        for (let i = 1; i <= 5; i++) {
            const starClass = i <= rating ? 'star-filled' : 'star-empty';
            starsHtml += `
                <svg class="${starClass}" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
            `;
        }
        
        // Форматируем цену
        const formattedPrice = formatPrice(product.price);
        const formattedOldPrice = product.old_price ? formatPrice(product.old_price) : '';
        
        // Определяем бейдж
        let badgeHtml = '';
        if (product.discount_percent) {
            badgeHtml = `<div class="product-badge">-${product.discount_percent}%</div>`;
        } else if (product.is_new) {
            badgeHtml = `<div class="product-badge" style="background: #0079D9;">Новинка</div>`;
        }
        
        return `
            <a href="${product.route || '#'}" class="product-card">
                <div class="product-image-container">
                    ${badgeHtml}
                    <img src="${product.image_url || '/images/default-product.jpg'}" 
                         alt="${product.title}" 
                         class="product-image"
                         onerror="this.src='/images/default-product.jpg'">
                </div>
                <div class="product-info">
                    <h3 class="product-name" title="${product.title}">${product.title}</h3>
                    
                    <div class="product-price">
                        ${formattedOldPrice ? `<span class="product-old-price">${formattedOldPrice}</span>` : ''}
                        <span>${formattedPrice}</span>
                    </div>
                    
                    ${product.rating > 0 ? `
                        <div class="product-rating">
                            <div class="stars">${starsHtml}</div>
                            <span class="rating-value">${product.rating.toFixed(1)}</span>
                            ${product.reviews_count ? `<span>(${product.reviews_count})</span>` : ''}
                        </div>
                    ` : ''}
                    
                    ${product.organization_name ? `
                        <div class="product-organization" title="${product.organization_name}">
                            ${product.organization_name}
                        </div>
                    ` : ''}
                    
                    <div class="product-meta">
                        ${product.category_name ? `<span class="product-category">${product.category_name}</span>` : ''}
                        ${product.in_stock !== undefined ? `
                            <span style="color: ${product.in_stock ? '#00C853' : '#F44336'}">
                                ${product.in_stock ? '✓ В наличии' : 'Нет в наличии'}
                            </span>
                        ` : ''}
                    </div>
                </div>
            </a>
        `;
    }
    
    // Форматирование цены
    function formatPrice(price) {
        if (!price) return '0 ₽';
        
        const number = parseFloat(price);
        if (isNaN(number)) return '0 ₽';
        
        // Форматируем с разделителями тысяч
        return number.toLocaleString('ru-RU', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        }) + ' ₽';
    }
    
    // Показать "не найдено"
    function showNoResults(query) {
        productsList.hide().empty();
        resultsCount.hide();
        queryText.text(query);
        noResultsMessage.show();
    }
    
    function showErrorMessage(message) {
        productsList.hide().empty();
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
        productsList.hide().empty();
        noResultsMessage.hide();
        resultsCount.hide();
    }
    
    // Инициализация
    searchInput.on('focus', function() {
        if ($(this).val().length >= 2 && productsList.children().length > 0) {
            showResults();
        }
    });
});
</script>