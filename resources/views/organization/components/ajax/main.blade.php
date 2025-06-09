<script>

$( ".li_cat_children_marketplace" ).on( "click", function() {

    let slug=$(this).attr('slug')

    if( $(this).parent('.ul_childern_cats_marketplace').siblings('.li_cat_main_marketplace').attr('id_category')==45){
        $( ".filter_block_organization select" ).removeClass('active_select_filter_organiaztion')
        $('#district_id').addClass('active_select_filter_organiaztion')
        $( ".filter_block_organization select" ).removeClass('active_select_filter_organiaztion_2')
        $('#district_id').addClass('active_select_filter_organiaztion_2')
        $('#label_select_1').hide()
        $('#label_select_2').show()
    }else{
        $( ".filter_block_organization select" ).removeClass('active_select_filter_organiaztion')
        $( ".filter_block_organization select" ).removeClass('active_select_filter_organiaztion_2')
        $('#cemetery_id').addClass('active_select_filter_organiaztion_2')
        $('#label_select_1').show()
        $('#label_select_2').hide()
    }

    $('.bac_loader').show()
    $('.load_block').show()
    let filter_work='off';
    if($('#filter_work').is(':checked')==true){
        filter_work='on';
    }
    let cemetery_id=null;
    let district_id=null;
    let filters={
        'filter_work':filter_work,
        'cemetery_id':cemetery_id,
        'district_id':district_id,
        'sort': $('.name_sort').attr('val'),
        "category_id": $(this).attr('id_category'),
    }
    if($('.active_select_filter_organiaztion').attr('name')=='cemetery_id'){
        district_id=null;
        cemetery_id=$('.active_select_filter_organiaztion').val()
        filters  = {
            'filter_work':filter_work,
            'cemetery_id':cemetery_id,
            'sort': $('.name_sort').attr('val'),
            "category_id": $(this).attr('id_category'),
        };
    }

    if($('.active_select_filter_organiaztion').attr('name')=='district_id'){
        district_id=$('.active_select_filter_organiaztion').val();
        cemetery_id=null
        filters  = {
            'filter_work':filter_work,
            'district_id':district_id,
            'sort': $('.name_sort').attr('val'),
            "category_id": $(this).attr('id_category'),
        };
    }
   
    

    let category_selected=$(this)
    $.ajax({
        type: 'GET',
        url: '{{route('organizations.ajax.filters')}}',
        data: filters,
        success: function (result) {
            $( ".li_cat_children_marketplace" ).removeClass('active_category')
            $('.li_cat_main_marketplace').removeClass('active_main_category')
            category_selected.parent().siblings('.li_cat_main_marketplace').addClass('active_main_category')
            category_selected.addClass('active_category')
            $('.ul_organizaiotns').html(result)
        },
        error: function () {
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
            alert('Ошибка');
        }
    });
    $.ajax({
        type: 'GET',
        url: '{{route('organizations.ajax.category-prices')}}',
        data: filters,
        success: function (result) {
            $('.block_table_price_orgniaztions').html(result)
           
            let strings = [];
            
            for (const [key, value] of Object.entries(filters)) {
                strings.push(key+"="+value)
            }
            let st = strings.join("&")
            window.history.pushState('organizations', 'Title', '/{{$city->slug}}/organizations/'+slug,+'?'+st);
        },
        error: function () {
            alert('Ошибка');
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
        }
    });

    $.ajax({
        type: 'GET',
        url: '{{route('organizations.ajax.title')}}',
        data: filters,
        success: function (result) {
           $('.content_order_page').html(result)
           $('.bac_loader').fadeOut()
           $('.load_block').fadeOut()
        },
        error: function () {
            alert('Ошибка');
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
        }
    });


    $.ajax({
        type: 'GET',
        url: '{{route('organizations.ajax.map')}}',
        data: filters,
        success: function (result) {
           $('.map_organizations').html(result)
           $('.bac_loader').fadeOut()
           $('.load_block').fadeOut()
        },
        error: function () {
            alert('Ошибка');
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
        }
    });

    $('.navigation_pages span').html(category_selected.html())


    
})

    



$( ".filter_block_organization select" ).on( "change", function() {
    $('.bac_loader').show()
    $('.load_block').show()
    $( ".filter_block_organization select" ).removeClass('active_select_filter_organiaztion')
    $(this).addClass('active_select_filter_organiaztion')

    let cemetery_id=null;
    let district_id=null;
    if($(this).attr('name')=='cemetery_id'){
        district_id=null;
        cemetery_id=$(this).val()
    }
    if($(this).attr('name')=='district_id'){
        district_id=$(this).val();
        cemetery_id=null
    }

    let filter_work='off';
    if($('#filter_work').is(':checked')==true){
        filter_work='on';
    }

    let filters  = {
        'filter_work':filter_work,
        'district_id':district_id,
        'cemetery_id':cemetery_id,
        'sort': $('.name_sort').attr('val'),
        "category_id": $('.active_category').attr('id_category'),
    };
    $.ajax({
        type: 'GET',
        url: '{{route('organizations.ajax.filters')}}',
        data: filters,
        success: function (result) {
            $('.ul_organizaiotns').html(result)
        },
        error: function () {
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
            alert('Ошибка');
        }
    });
    $.ajax({
        type: 'GET',
        url: '{{route('organizations.ajax.category-prices')}}',
        data: filters,
        success: function (result) {
            $('.block_table_price_orgniaztions').html(result)
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
            let strings = [];
            for (const [key, value] of Object.entries(filters)) {
                strings.push(key+"="+value)
            }
            let st = strings.join("&")
            window.history.pushState('organizations', 'Title', '/{{$city->slug}}/organizations/'+$('.active_category').attr('slug')+'?'+st);
        },
        error: function () {
            alert('Ошибка');
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
        }
    });
    $.ajax({
        type: 'GET',
        url: '{{route('organizations.ajax.title')}}',
        data: filters,
        success: function (result) {
           $('.content_order_page').html(result)
           $('.bac_loader').fadeOut()
           $('.load_block').fadeOut()
        },
        error: function () {
            alert('Ошибка');
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
        }
    });
    $.ajax({
        type: 'GET',
        url: '{{route('organizations.ajax.map')}}',
        data: filters,
        success: function (result) {
           $('.map_organizations').html(result)
           $('.bac_loader').fadeOut()
           $('.load_block').fadeOut()
        },
        error: function () {
            alert('Ошибка');
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
        }
    });
})




// $( ".filter_sort .li_sort" ).on( "click", function() {

//         $('.bac_loader').show()
//         $('.load_block').show()

//         let cemetery_id=null;
//         let district_id=null;

//         if($('.active_select_filter_organiaztion').attr('name')=='cemetery_id'){
//             district_id=null;
//             cemetery_id=$('.active_select_filter_organiaztion').val()
//         }

//         $('.name_sort').html($(this).html())
//         $('.name_sort').attr('val', $(this).attr('val'))

//         let filter_work='off';
//         if($('#filter_work').is(':checked')==true){
//             filter_work='on';
//         }


//         let filters  = {
//             'filter_work':filter_work,
//             'district_id':district_id,
//             'cemetery_id':cemetery_id,
//             'sort':$(this).attr('val'),
//             "category_id": $('.active_category').attr('id_category'),
//         };
//         $.ajax({
//             type: 'GET',
//             url: '{{route('organizations.ajax.filters')}}',
//             data: filters,
//             success: function (result) {
//                 $('.ul_organizaiotns').html(result)
//                 let strings = [];
//                 for (const [key, value] of Object.entries(filters)) {
//                     strings.push(key+"="+value)
//                 }
//                 let st = strings.join("&")
//                 window.history.pushState('organizations', 'Title', '/{{$city->slug}}/organizations/'+$('.active_category').attr('slug')+'?'+st);
//                 $('.bac_loader').fadeOut()
//                 $('.load_block').fadeOut()

//             },
//             error: function () {
//                 $('.bac_loader').fadeOut()
//                 $('.load_block').fadeOut()
//                 alert('Ошибка');
//             }
//         });
        
// })



// $( "#filter_work" ).on( "change", function() {
//     $('.bac_loader').show()
//     $('.load_block').show()

//     let filter_work='off';
//     if($(this).is(':checked')==true){
//         filter_work='on';
//     }
//     let cemetery_id=null;
//     let district_id=null;
//     let filters={
//         'filter_work':filter_work,
//         'cemetery_id':cemetery_id,
//         'district_id':district_id,
//         'sort': $('.name_sort').attr('val'),
//         "category_id": $('.active_category').attr('id_category'),
//     }
//     if($('.active_select_filter_organiaztion').attr('name')=='cemetery_id'){
//         district_id=null;
//         cemetery_id=$('.active_select_filter_organiaztion').val()
//         filters  = {
//             'filter_work':filter_work,
//             'cemetery_id':cemetery_id,
//             'sort': $('.name_sort').attr('val'),
//             "category_id": $('.active_category').attr('id_category'),
//         };
//     }

//     if($('.active_select_filter_organiaztion').attr('name')=='district_id'){
//         district_id=$('.active_select_filter_organiaztion').val();
//         cemetery_id=null
//         filters  = {
//             'filter_work':filter_work,
//             'district_id':district_id,
//             'sort': $('.name_sort').attr('val'),
//             "category_id": $('.active_category').attr('id_category'),
//         };
//     }
   
   
//     $.ajax({
//         type: 'GET',
//         url: '{{route('organizations.ajax.filters')}}',
//         data: filters,
//         success: function (result) {
//             $('.ul_organizaiotns').html(result)
//             let strings = [];
//             for (const [key, value] of Object.entries(filters)) {
//                 strings.push(key+"="+value)
//             }
//             let st = strings.join("&")
//             window.history.pushState('organizations', 'Title', '/{{$city->slug}}/organizations/'+$('.active_category').attr('slug')+'?'+st);
//             $('.bac_loader').fadeOut()
//             $('.load_block').fadeOut()

//         },
//         error: function () {
//             $('.bac_loader').fadeOut()
//             $('.load_block').fadeOut()
//             alert('Ошибка');
//         }
//     });
//     $.ajax({
//         type: 'GET',
//         url: '{{route('organizations.ajax.map')}}',
//         data: filters,
//         success: function (result) {
//            $('.map_organizations').html(result)
//            $('.bac_loader').fadeOut()
//            $('.load_block').fadeOut()
//         },
//         error: function () {
//             alert('Ошибка');
//             $('.bac_loader').fadeOut()
//             $('.load_block').fadeOut()
//         }
//     });
// })
</script>


<script>
$(document).ready(function() {
    // ID категорий, для которых нужно показывать фильтр районов (например, организация поминок)
    const DISTRICT_CATEGORIES = [45]; 

    // Функция для получения текущих фильтров в зависимости от ширины экрана
    function getCurrentFilters() {
        if (window.innerWidth < 760) {
            // Мобильная версия
            return {
                cemetery_id: $('.cemetery-select .mobile_filter_option.active').data('id') || null,
                district_id: $('.district-select .mobile_filter_option.active').data('id') || null,
                category_id: $('.subcategory-select .mobile_filter_option.active').data('id') || null,
                category_slug: $('.subcategory-select .mobile_filter_option.active').data('slug') || null,
                filter_work: $('.filter_work').is(':checked') ? 'on' : 'off',
                sort: $('.name_sort').attr('val') || 'date'
            };
        } else {
            // Десктопная версия
            return {
                cemetery_id: $('.active_select_filter_organiaztion[name="cemetery_id"]').val() || null,
                district_id: $('.active_select_filter_organiaztion[name="district_id"]').val() || null,
                category_id: $('.active_category').attr('id_category') || null,
                category_slug: $('.active_category').attr('slug') || null,
                filter_work: $('#filter_work').is(':checked') ? 'on' : 'off',
                sort: $('.name_sort').attr('val') || 'date'
            };
        }
    }

    // Общая функция для применения фильтров
    function applyCommonFilters(filters) {
        $('.bac_loader').show();
        $('.load_block').show();

        // Удаляем null значения
        Object.keys(filters).forEach(key => {
            if (filters[key] === null || filters[key] === undefined) {
                delete filters[key];
            }
        });

        // AJAX запрос для обновления списка организаций
        $.ajax({
            type: 'GET',
            url: '{{route("organizations.ajax.filters")}}',
            data: filters,
            success: function(result) {
                $('.ul_organizaiotns').html(result);
                
                // Обновляем URL только если есть slug категории
                if (filters.category_slug) {
                    let strings = [];
                    for (const [key, value] of Object.entries(filters)) {
                        if (key !== 'category_slug') {
                            strings.push(key + "=" + value);
                        }
                    }
                    let st = strings.join("&");
                    window.history.pushState('organizations', 'Title', '/{{$city->slug}}/organizations/' + filters.category_slug + (st ? '?' + st : ''));
                }
            },
            error: function() {
                alert('Ошибка при загрузке организаций');
            },
            complete: function() {
                $('.bac_loader').fadeOut();
                $('.load_block').fadeOut();
            }
        });

        // AJAX запрос для обновления заголовка
        $.ajax({
            type: 'GET',
            url: '{{route("organizations.ajax.title")}}',
            data: filters,
            success: function (result) {
                $('.content_order_page').html(result)
                 $('.bac_loader').fadeOut()
           $('.load_block').fadeOut()
            },
            error: function() {
                console.error('Ошибка при загрузке заголовка');
            }
        });
        
        // AJAX запрос для обновления таблицы цен
        $.ajax({
            type: 'GET',
            url: '{{route("organizations.ajax.category-prices")}}',
            data: filters,
            success: function (result) {
                $('.block_table_price_orgniaztions').html(result)
                 $('.bac_loader').fadeOut()
           $('.load_block').fadeOut()
            },
            error: function() {
                console.error('Ошибка при загрузке цен');
            }
        });
        

        // AJAX запрос для обновления карты
        $.ajax({
            type: 'GET',
            url: '{{route("organizations.ajax.map")}}',
            success: function (result) {
                $('.map_organizations').html(result)
                 $('.bac_loader').fadeOut()
           $('.load_block').fadeOut()
            },
            data: filters,
            
            error: function() {
                console.error('Ошибка при загрузке карты');
            }
        });
    }

    // Обработка сортировки (общая для мобильной и десктопной версии)
    $(document).on('click', '.filter_sort .li_sort', function() {
        const filters = getCurrentFilters();
        filters.sort = $(this).attr('val') || $(this).data('val');
        $('.name_sort').html($(this).html()).attr('val', filters.sort);
        
        applyCommonFilters(filters);
    });

    // Обработка чекбокса "работает сейчас" (общая для мобильной и десктопной версии)
    $(document).on('change', '#filter_work', function() {
        const filters = getCurrentFilters();
        filters.filter_work = $(this).is(':checked') ? 'on' : 'off';
        
        applyCommonFilters(filters);
    });

    // Остальные обработчики для мобильной версии
    $(document).on('click', '.block_filter_mobile', function(e) {
        if (!$(e.target).closest('.mobile_filter_option').length) {
            $(this).find('.mobile_filter_select').slideToggle();
            $(this).find('.open_mobile_filter_select').toggleClass('rotate');
        }
    });

    $(document).on('click', '.category-select .mobile_filter_option', function(e) {
        e.stopPropagation();
        const catId = $(this).data('id');
        const catTitle = $(this).text();
        
        $('.category-title').text(catTitle);
        $('.category-select').slideUp();
        $('.category-select').siblings('.open_mobile_filter_select').removeClass('rotate');
        
        $('.subcategory-select').html('<div class="loading-options">Загрузка...</div>').slideDown();
        
        $.ajax({
            url: '{{ route("category.product.children.ul.filter") }}',
            method: 'GET',
            data: { category_id: catId },
            success: function(response) {
                let optionsHtml = '';
                
                if (response.length > 0) {
                    response.forEach(function(subcat) {
                        optionsHtml += `<div class="mobile_filter_option" data-id="${subcat.id}" data-slug="${subcat.slug}">${subcat.title}</div>`;
                    });
                    
                    const firstSubcat = response[0];
                    $('.subcategory-title').text(firstSubcat.title);
                    toggleDistrictFilter(catId);
                    
                    // Применяем фильтры с первой подкатегорией
                    const filters = getCurrentFilters();
                    filters.category_id = firstSubcat.id;
                    filters.category_slug = firstSubcat.slug;
                    applyCommonFilters(filters);
                } else {
                    optionsHtml = '<div class="no-options">Нет подкатегорий</div>';
                    $('.subcategory-title').text('Не выбрано');
                }
                
                $('.subcategory-select').html(optionsHtml);
            },
            error: function() {
                $('.subcategory-select').html('<div class="error-options">Ошибка загрузки</div>');
            }
        });
    });

    $(document).on('click', '.subcategory-select .mobile_filter_option', function(e) {
        $('.subcategory-select .mobile_filter_option').removeClass('active')
        $(this).addClass('active')
        e.stopPropagation();
        const subcatId = $(this).data('id');
        const subcatSlug = $(this).data('slug');
        const subcatTitle = $(this).text();
    
        $('.subcategory-title').text(subcatTitle);
        $('.html_navigation span').text(subcatTitle);
        $('.subcategory-select').slideUp();
        $('.subcategory-select').siblings('.open_mobile_filter_select').removeClass('rotate');
        
        const filters = getCurrentFilters();
        filters.category_id = subcatId;
        filters.category_slug = subcatSlug;
        applyCommonFilters(filters);
    });

    $(document).on('click', '.cemetery-select .mobile_filter_option', function(e) {
        $('.cemetery-select .mobile_filter_option').removeClass('active')
        $(this).addClass('active')
        e.stopPropagation();
        const cemeteryId = $(this).data('id');
        const cemeteryTitle = $(this).text();
        
        $('.cemetery-title').text(cemeteryTitle);
        $('.cemetery-select').slideUp();
        $('.cemetery-select').siblings('.open_mobile_filter_select').removeClass('rotate');
        
        const filters = getCurrentFilters();
        filters.cemetery_id = cemeteryId;
        filters.district_id = null;
        applyCommonFilters(filters);
    });

    $(document).on('click', '.district-select .mobile_filter_option', function(e) {
        $('.district-select .mobile_filter_option').removeClass('active')
        $(this).addClass('active')
        e.stopPropagation();
        const districtId = $(this).data('id');
        const districtTitle = $(this).text();
        
        $('.district-title').text(districtTitle);
        $('.district-select').slideUp();
        $('.district-select').siblings('.open_mobile_filter_select').removeClass('rotate');
        
        const filters = getCurrentFilters();
        filters.district_id = districtId;
        filters.cemetery_id = null;
        applyCommonFilters(filters);
    });

    function toggleDistrictFilter(categoryId) {
        if (DISTRICT_CATEGORIES.includes(parseInt(categoryId))) {
            $('.cemetery-filter').hide();
            $('.district-filter').show();
            $('.cemetery-title').text('Выберите кладбище');
            $('.cemetery-select .mobile_filter_option').removeClass('active');
            $('.cemetery-select .mobile_filter_option[data-id="0"]').addClass('active');
        } else {
            $('.cemetery-filter').show();
            $('.district-filter').hide();
            $('.district-title').text('Выберите район');
            $('.district-select .mobile_filter_option').removeClass('active');
            $('.district-select .mobile_filter_option[data-id="0"]').addClass('active');
        }
    }

    // Инициализация при загрузке страницы
    function initFilters() {
        const initialCategoryId = "{{ $category ? $category->parent_id : ($cats->first() ? $cats->first()->id : '') }}";
        if (initialCategoryId) {
            toggleDistrictFilter(initialCategoryId);
        }
        
        @if($category)
            $(`.category-select .mobile_filter_option[data-id="{{ $category->parent_id }}"]`).addClass('active');
            $(`.subcategory-select .mobile_filter_option[data-id="{{ $category->id }}"]`).addClass('active');
        @endif
        
        @if($cemetery_choose)
            $(`.cemetery-select .mobile_filter_option[data-id="{{ $cemetery_choose->id }}"]`).addClass('active');
        @endif
        
        @if($district_choose)
            $(`.district-select .mobile_filter_option[data-id="{{ $district_choose->id }}"]`).addClass('active');
        @endif
    }

    initFilters();
});
</script>