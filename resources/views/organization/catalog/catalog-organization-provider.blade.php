@include('header.header')

<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
           {{view('organization.components.catalog-provider.title-page',compact('title_h1','city','category','category_main'));}}
        </div>
        <img class='rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
    </div>
</section>


<section class="organization_marketplace">
    <div class="container">
        <div class="grid_product_two">
            <div class="one_block_market">
                <div class="block_table_price_orgniaztions">
                    {{view('organization.components.catalog-provider.prices',compact('price_min','price_middle','price_max','city','category'));}}
                   
                </div>
                {{view('organization.components.catalog-provider.filters',compact('sort','city_all','city','filter_work'))}}

                
                <div class="ul_organizaiotns">
                    {{view('organization.components.catalog-provider.organizations-show',compact('organizations_category'))}}
                </div>
                

            </div>
            <div class="sidebar">
                {{view('organization.components.catalog-provider.sidebar',compact('cats','category'))}}
            </div>
        </div>
</section>

<section>
    <div class="container">
        <div class="gos_block">
            <img src="{{asset('storage/uploads/image 171.png')}}" alt="">  
            <div class="content_gos_block">
                <div class="title_green_big">Заказчик хочет другой памятник, но у вас его нет?</div>    
                <div class="text_black">Приложите картинку памятника и сделайте запрос по всем поставщикам или иного товара</div>
            </div>  
            <img class='btn_img_gos_block' src='{{asset('storage/uploads/Переключатель.svg')}}'>    
        </div>
    </div>
</section>



<div class="rating_block">
    {{view('organization.components.catalog-provider.rating-price-organizations',compact('oragnizations_rating','category','city'))}}
</div>


<section class="block">
    <div class="container">
        <div class="grid_two mobile_block_info_1_grid">
            <img src="{{asset('storage/uploads/002-spisok-uslug-2 2.png')}}" alt="" class="img_text_block">
            <div class="text_block_index">
                <div class="title_text_block">Получите прямой расчёт
                    от 10 проверенных ритуальных агентств по низким ценам
                </div>
                <div class="blue_btn" data-bs-toggle="modal" data-bs-target="#beautification_form">Получить расчет</div>
            </div>
        </div>
    </div>
</section>




@include('footer.footer') 






<script>

$( ".li_cat_children_marketplace" ).on( "click", function() {

    $('.bac_loader').show()
    $('.load_block').show()

    let filter_work='off';

    if($('#filter_work').is(':checked')==true){
        filter_work='on';
    }

    let filters  = {
        'city_id':$('.filter_block_organization select.active_select_filter_organiaztion_2').val(),
        'sort': $('.name_sort').attr('val'),
        'filter_work':filter_work,
        "category_id": $(this).attr('id_category'),
    };    

    let category_selected=$(this)

    $.ajax({
        type: 'GET',
        url: '{{route('organizations.provider.ajax.filters')}}',
        data: filters,
        success: function (result) {
            $( ".li_cat_children_marketplace" ).removeClass('active_category')
            $('.li_cat_main_marketplace').removeClass('active_main_category')
            category_selected.parent().siblings('.li_cat_main_marketplace').addClass('active_main_category')
            category_selected.addClass('active_category')
            $('.ul_organizaiotns').html(result)
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
        },
        error: function () {
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
            alert('Ошибка');
        }
    });

    $.ajax({
        type: 'GET',
        url: '{{route('organizations.provider.ajax.rating')}}',
        data: filters,
        success: function (result) {
          $('.rating_block').html(result)
          $('.bac_loader').fadeOut()
          $('.load_block').fadeOut()
        },
        error: function () {
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
            alert('Ошибка');
        }
    });

    $.ajax({
        type: 'GET',
        url: '{{route('organizations.provider.ajax.category-prices')}}',
        data: filters,
        success: function (result) {
            $('.block_table_price_orgniaztions').html(result)
           
            let strings = [];
            
            for (const [key, value] of Object.entries(filters)) {
                strings.push(key+"="+value)
            }
            let st = strings.join("&")
            window.history.pushState('organizations-provider', 'Title', '/{{$city->slug}}/organizations-provider?'+st);
        },
        error: function () {
            alert('Ошибка');
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
        }
    });

    $.ajax({
        type: 'GET',
        url: '{{route('organizations.provider.ajax.title')}}',
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


    
})

    



$( ".filter_block_organization select" ).on( "change", function() {

    $('.bac_loader').show()
    $('.load_block').show()
    $( ".filter_block_organization select" ).removeClass('active_select_filter_organiaztion')
    $(this).addClass('active_select_filter_organiaztion')

    let filter_work='off';

    if($('#filter_work').is(':checked')==true){
        filter_work='on';
    }
    let filters  = {
        'city_id':$('.filter_block_organization select.active_select_filter_organiaztion_2').val(),
        'sort': $('.name_sort').attr('val'),
        'filter_work':filter_work,
        "category_id": $('.active_category').attr('id_category'),
    };
    $.ajax({
        type: 'GET',
        url: '{{route('organizations.provider.ajax.filters')}}',
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
        url: '{{route('organizations.provider.ajax.category-prices')}}',
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
            window.history.pushState('organizations-provider', 'Title', '/{{$city->slug}}/organizations-provider?'+st);
        },
        error: function () {
            alert('Ошибка');
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
        }
    });
    $.ajax({
        type: 'GET',
        url: '{{route('organizations.provider.ajax.title')}}',
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
})




$( ".filter_sort .li_sort" ).on( "click", function() {

        $('.bac_loader').show()
        $('.load_block').show()
        

        let filter_work='off';
        if($('#filter_work').is(':checked')==true){
            filter_work='on';
        }

        $('.name_sort').html($(this).html())
        $('.name_sort').attr('val', $(this).attr('val'))

        let filters  = {
            'city_id':$('.filter_block_organization select.active_select_filter_organiaztion_2').val(),
            'sort': $(this).attr('val'),
            'filter_work':filter_work,
            "category_id": $('.active_category').attr('id_category'),
        };
        $.ajax({
            type: 'GET',
            url: '{{route('organizations.provider.ajax.filters')}}',
            data: filters,
            success: function (result) {
                $('.ul_organizaiotns').html(result)
                let strings = [];
                for (const [key, value] of Object.entries(filters)) {
                    strings.push(key+"="+value)
                }
                let st = strings.join("&")
                window.history.pushState('organizations-provider', 'Title', '/{{$city->slug}}/organizations-provider?'+st);
                $('.bac_loader').fadeOut()
                $('.load_block').fadeOut()

            },
            error: function () {
                $('.bac_loader').fadeOut()
                $('.load_block').fadeOut()
                alert('Ошибка');
            }
        });
})




$( "#filter_work" ).on( "change", function() {
    $('.bac_loader').show()
    $('.load_block').show()

    let filter_work='off';
    if($(this).is(':checked')==true){
        filter_work='on';
    }
    let filters  = {
        'city_id':$('.filter_block_organization select.active_select_filter_organiaztion_2').val(),
        'sort': $('.name_sort').attr('val'),
        'filter_work':filter_work,
        "category_id": $('.active_category').attr('id_category'),
    };
    $.ajax({
        type: 'GET',
        url: '{{route('organizations.provider.ajax.filters')}}',
        data: filters,
        success: function (result) {
            $('.ul_organizaiotns').html(result)
            let strings = [];
            for (const [key, value] of Object.entries(filters)) {
                strings.push(key+"="+value)
            }
            let st = strings.join("&")
            window.history.pushState('organizations-provider', 'Title', '/{{$city->slug}}/organizations-provider?'+st);
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()

        },
        error: function () {
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
            alert('Ошибка');
        }
    });
    $.ajax({
        type: 'GET',
        url: '{{route('organizations.provider.ajax.category-prices')}}',
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
            window.history.pushState('organizations-provider', 'Title', '/{{$city->slug}}/organizations-provider?'+st);
        },
        error: function () {
            alert('Ошибка');
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
        }
    });
})


$( "#name_organization" ).on( "input", function() {
    $('.bac_loader').show()
    $('.load_block').show()

    let filters  = {
        "name_organization": $(this).val(),
    };

    $.ajax({
        type: 'GET',
        url: '{{route('organizations.provider.ajax.search')}}',
        data: filters,
        success: function (result) {
            $('.ul_organizaiotns').html(result)
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
            let strings = [];
                    for (const [key, value] of Object.entries(filters)) {
                        strings.push(key+"="+value)
                    }
                    let st = strings.join("&")
                    window.history.pushState('organizations-provider', 'Title', '/{{$city->slug}}/organizations-provider?'+st);
                },
        error: function () {
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
            alert('Ошибка');
        }
    });
})
</script>