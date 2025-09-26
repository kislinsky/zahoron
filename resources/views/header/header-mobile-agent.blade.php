
<div class="mobile_header avito_style">
    <!-- Шапка меню -->
    <div class="mobile_header_top">
        <div data-bs-dismiss="modal" class="close_mobile_header">
            <span class="close_icon">×</span>
        </div>
        <div class="city_selector city_selected" id_city_selected='{{ $city->id }}'>
            <span class="city_name">{{ $city->title }}</span>
            <span class="city_arrow">▼</span>
        </div>
    </div>

    <!-- Основное меню -->
    <div class="mobile_menu_content">
        <?php $pages = mobilePagesAccountAgent(); ?>
        
        @foreach ($pages as $page)
        <div class="menu_section">
            @if(isset($page[1]))
                <div class="menu_item with_arrow" onclick="toggleSubmenu(this)">
                    <div class="menu_text">
                        <span class="menu_title">{{ $page[0][0] }}</span>
                        <span class="menu_badge">{{ count($page[1]) }}</span>
                    </div>
                    <span class="menu_arrow">›</span>
                </div>
                <div class="submenu">
                    @foreach($page[1] as $children_page)
                    <a href="{{ $children_page[1] }}" class="submenu_item">
                        <span class="submenu_text">{{ $children_page[0] }}</span>
                        <span class="submenu_arrow">›</span>
                    </a>
                    @endforeach
                </div>
            @else
                <a href="{{ $page[0][1] }}" class="menu_item">
                    <span class="menu_title">{{ $page[0][0] }}</span>
                </a>
            @endif
        </div>
        @endforeach

        <!-- Выйти -->
        <div class="menu_section logout_section">
            <a href="{{ route('logout') }}" class="menu_item logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <span class="menu_title logout_text">Выйти</span>
            </a>
        </div>
    </div>
</div>


<script>
function toggleSubmenu(element) {
    const submenu = element.nextElementSibling;
    const isExpanded = submenu.classList.contains('expanded');
    
    // Закрываем все открытые подменю
    document.querySelectorAll('.submenu.expanded').forEach(menu => {
        menu.classList.remove('expanded');
    });
    document.querySelectorAll('.menu_item.active').forEach(item => {
        item.classList.remove('active');
    });
    
    // Открываем текущее, если было закрыто
    if (!isExpanded) {
        element.classList.add('active');
        submenu.classList.add('expanded');
    }
}

// Закрытие при клике вне меню
document.addEventListener('click', function(e) {
    if (!e.target.closest('.menu_item')) {
        document.querySelectorAll('.submenu.expanded').forEach(menu => {
            menu.classList.remove('expanded');
        });
        document.querySelectorAll('.menu_item.active').forEach(item => {
            item.classList.remove('active');
        });
    }
});
</script>