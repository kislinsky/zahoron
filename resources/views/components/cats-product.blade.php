<?php 
$cats = mainCategoryProduct();
?>

@if(count($cats) > 0)
<section class='cats_product'>
    <div class="container">
        <h2 class="title_our_works">Удобный выбор категорий</h2>
        
        <!-- Кнопки переключения типа категорий -->
        <div class="category-type-switcher">
            <button class="category-type-btn active" data-type="ritual" data-route="organizations.category">
                Ритуальные услуги
            </button>
            <button class="category-type-btn" data-type="improvement" data-route="marketplace.category">
                Облагороживание
            </button>
        </div>
        
        <!-- Горизонтальный скролл главных категорий -->
        <div class="main-categories-scroll">
            <div class="main-categories-container">
                @foreach ($cats as $index => $cat)
                    @if($cat->display == 1)
                    <button class="main-category-btn {{ $index === 0 ? 'active' : '' }}" 
                            data-category-id="{{ $cat->id }}"
                            data-slug="{{ $cat->slug }}">
                        {{ $cat->title }}
                    </button>
                    @endif
                @endforeach
            </div>
        </div>
        
        <!-- Контейнер для подкатегорий -->
        <div class="subcategories-container">
            @foreach ($cats as $index => $cat)
                @if($cat->display == 1)
                <?php $cats_children = childrenCategoryProducts($cat); ?>
                <div class="subcategories-grid {{ $index === 0 ? 'active' : '' }}" 
                     id="subcategories-{{ $cat->id }}">
                    @if (count($cats_children) > 0)
                        @foreach ($cats_children as $cat_children)
                            @if($cat_children->display == 1)
                                <a href="#" class="subcategory-link" 
                                   data-route="organizations.category" 
                                   data-slug="{{ $cat_children->slug }}">
                                    <div class="text_black">{{ $cat_children->title }}</div>
                                </a>
                            @endif
                        @endforeach
                    @else
                        <p class="no-subcategories">Нет доступных подкатегорий</p>
                    @endif
                </div>
                @endif
            @endforeach
        </div>
    </div>
</section>

<style>
/* Стили для переключателя типа категорий */
.category-type-switcher {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    justify-content: start;
}

.category-type-btn {
    padding: 10px 20px;
    border: 2px solid #0080D7;
    background: white;
    color: #007bff;
    border-radius: 15px;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.3s ease;
    font-family: inherit;
}

.category-type-btn.active {
    background: #0080D7;
    color: white;
}

.category-type-btn:hover {
    background: #0080D7;
    color: white;
}

/* Горизонтальный скролл главных категорий */
.main-categories-scroll {
    overflow-x: auto;
    margin-bottom: 15px;
    padding-bottom: 10px;
}

.main-categories-scroll::-webkit-scrollbar {
    height: 6px;
}

.main-categories-scroll::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.main-categories-scroll::-webkit-scrollbar-thumb {
    background: #007bff;
    border-radius: 3px;
}

.main-categories-container {
    display: flex;
    gap: 15px;
    padding: 10px 0;
    min-width: min-content;
}

.main-category-btn {
    color: black;
    cursor: pointer;
    white-space: nowrap;
    font-size: 16px;
    transition: all 0.3s ease;
    flex-shrink: 0;
    background:none;
    outline:none;
    border:none;
    text-decoration: underline;
    font-family: inherit;
}


/* Контейнер подкатегорий */
.subcategories-container {
    min-height: 100px;
    max-width: 70%;
    position: relative;
}

.subcategories-grid {
    flex-direction: column-reverse;
    gap: 15px;
    animation: fadeIn 0.3s ease;
    display: none;
}

.subcategories-grid.active {
    display: flex;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.subcategory-link {
    display: block;
    padding: 12px 15px;
    background: white;
    border-radius: 6px;
    text-decoration: none;
    font-size:16px;
    color: black;
    border: 2px solid #0080D7;
    transition: all 0.3s ease;
    text-align: start;
}

.subcategory-link:hover {
    background: #0080D7;
    color: white !important;
    border-color: #0080D7;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
}

.no-subcategories {
    text-align: center;
    color: #666;
    font-style: italic;
    grid-column: 1 / -1;
}

/* Адаптивность */
@media (max-width: 768px) {
    .category-type-switcher {
        flex-direction: column;
    }
    
    .main-category-btn {
        padding: 10px 15px;
        font-size: 14px;
    }
    
    .subcategories-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Текущие настройки
    let currentType = 'ritual';
    let currentRoute = 'organizations.category';
    
    // Все кнопки переключения типа
    const typeButtons = document.querySelectorAll('.category-type-btn');
    
    // Все главные категории
    const mainCategoryButtons = document.querySelectorAll('.main-category-btn');
    
    // Все контейнеры подкатегорий
    const subcategoryGrids = document.querySelectorAll('.subcategories-grid');
    
    // Все ссылки подкатегорий
    const subcategoryLinks = document.querySelectorAll('.subcategory-link');
    
    // Обработчики для кнопок типа категорий
    typeButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Убираем активный класс со всех кнопок
            typeButtons.forEach(btn => btn.classList.remove('active'));
            
            // Активируем нажатую кнопку
            this.classList.add('active');
            
            // Обновляем текущие настройки
            currentType = this.dataset.type;
            currentRoute = this.dataset.route;
            
            // Обновляем все ссылки подкатегорий
            updateAllSubcategoryLinks();
        });
    });
    
    // Обработчики для главных категорий
    mainCategoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Убираем активный класс со всех кнопок
            mainCategoryButtons.forEach(btn => btn.classList.remove('active'));
            
            // Активируем нажатую кнопку
            this.classList.add('active');
            
            // Получаем ID категории
            const categoryId = this.dataset.categoryId;
            
            // Показываем соответствующие подкатегории
            showSubcategories(categoryId);
        });
    });
    
    // Функция показа подкатегорий
    function showSubcategories(categoryId) {
        // Скрываем все контейнеры подкатегорий
        subcategoryGrids.forEach(grid => {
            grid.classList.remove('active');
        });
        
        // Показываем нужный контейнер
        const targetGrid = document.getElementById(`subcategories-${categoryId}`);
        if (targetGrid) {
            targetGrid.classList.add('active');
            
            // Обновляем ссылки в этом контейнере
            updateSubcategoryLinks(targetGrid);
        }
    }
    
    // Функция обновления ссылок в конкретном контейнере
    function updateSubcategoryLinks(container) {
        const links = container.querySelectorAll('.subcategory-link');
        links.forEach(link => {
            updateLinkHref(link);
        });
    }
    
    // Функция обновления всех ссылок подкатегорий
    function updateAllSubcategoryLinks() {
        subcategoryLinks.forEach(link => {
            updateLinkHref(link);
        });
    }
    const currentCitySlug = '{{ selectCity()->slug }}';
    
   // Функция обновления одной ссылки
function updateLinkHref(link) {
    const slug = link.dataset.slug;
    const baseUrl = window.location.origin; // Получаем текущий домен
    
    if (currentRoute === 'organizations.category') {
        link.href = `${baseUrl}/${currentCitySlug}/organizations/${slug}`;
    } else if (currentRoute === 'marketplace.category') {
        link.href = `${baseUrl}/${currentCitySlug}/marketplace/${slug}`;
    }
    
    // Обновляем data-route для ссылки
    link.dataset.route = currentRoute;
}
    
    // Обработчики для ссылок подкатегорий
    subcategoryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Можно добавить дополнительную логику при клике
            console.log('Переход к категории:', this.dataset.slug);
            console.log('Тип категории:', currentType);
            console.log('Маршрут:', currentRoute);
        });
    });
    
    // Инициализация: активируем первую категорию
    if (mainCategoryButtons.length > 0) {
        const firstActiveButton = document.querySelector('.main-category-btn.active');
        if (firstActiveButton) {
            const firstCategoryId = firstActiveButton.dataset.categoryId;
            showSubcategories(firstCategoryId);
        }
    }
    
    // Обновляем все ссылки при загрузке
    updateAllSubcategoryLinks();
});
</script>
@endif