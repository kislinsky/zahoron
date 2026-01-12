@include('header.header')

@include('forms.cemetery-form')


<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
        {{ view("product.components.catalog.title", compact('title_h1',"category",'cemetery','city','district')) }}
        </div>
        <img class='img_light_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        <img class='img_black_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1_black.svg')}}" alt="">
    </div>
</section>

<div class="html_navigation">
    {{view('components.navigation',compact('pages_navigation'))}}
</div>

<section class="product_market">
    <div class="container">
       
        <div class="grid_product_two">
            <div class="one_block_market">
                @include('components.product-search', [
                    'placeholder' => 'Поиск товаров...',
                    'containerClass' => 'filter_block',
                ])
                {{ view("product.components.catalog.filters", compact('layerings','sort','materials_filter','category','cemeteries_all','districts_all','cemetery','district')) }}
                <div class="html_products_wrap">
                    @if(isset($category) && $category!=null )
                        @if($category->parent_id==36)
                            {{ view("product.components.catalog.products-show-beautification", compact("products",'category')) }}
                        @endif
                        @if($category->parent_id==31)
                            {{ view("product.components.catalog.products-show-funeral-service", compact("products",'category')) }}
                        @endif
                        @if($category->parent_id==45)
                            {{ view("product.components.catalog.products-show-organization-commemorations", compact("products",'category')) }}
                        @endif
                    @endif

                </div>
            </div>
            <div class="sidebar">
                {{ view("product.components.catalog.sidebar", compact("cats",'category')) }}
            </div>
            
        </div>
        
    </div>
</section>


<div class="html_cemetery_wrap">
    {{ view("product.components.catalog.cemetery-show", compact("cemetery")) }}
</div>
<div class="html_faqs_wrap">
    {{ view("product.components.catalog.faq-show", compact("faqs")) }}
</div>

<div class="html_cat_reviews_wrap">
    {{view('product.components.catalog.reviews',compact('reviews'))}} 
</div>

<div class="html_cat_manual_wrap">
    {{ view("product.components.catalog.cat-manual-show", compact("category")) }}
</div>


@include('forms.search-form') 
<div class="html_cat_content_wrap">
    {{ view("product.components.catalog.cat-content-show", compact("category")) }}

</div>



<section>
    <div class="container">
        <div class="gos_block gos_block_1">
            <img src="{{asset('storage/uploads/image 29.png')}}" alt="">  
            <div class="content_gos_block">
                <h2 class="title_blue_big">Государственные выплаты <span class='title_green_big'>+ 13500 рублей</span></h2>    
                <div class="text_gray">Выплаты производятся умершиим не работающим пенсионерам</div>
            </div>      
        </div>
    </div>
</section>



@include('components.rating-funeral-agencies-prices')

<section class="block">
    <div class="container">
        <div class="grid_two mobile_block_info_1_grid">
            <img src="{{asset('storage/uploads/002-spisok-uslug-2 1.png')}}" alt="" class="img_text_block">
            <div class="text_block_index">
                <h2 class="title_text_block">Получите расчет стоимости ритуальных
                    услуг от 10 проверенных организаций 
                    без дополнительных услуг
                </h2>
                <div class="blue_btn open_shipping_200">Сэкономить до 20 000 руб.</div>
            </div>
        </div>
    </div>
</section>



@include('components.funeral-service-marketplace')

@include('components.rating-uneral-bureaus-raves-prices')

@include('components.monuments-grave')


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

@include('components.rating-establishments-providing-halls-holding-commemorations')

@include('components.memorial-dinners-marketplace')

@include('components.memorial-hall-marketplace')

@include('components.rewies') 

@include('components.reviews-funeral-organization') 

@include('components.map-morgues') 

@include('components.map-cemeteries') 


@include('components.faq') 



@if($city->text_about_project!=null)
    <section class="about_company bac_gray">
        <div class="container">
            <div class="title">О проекте "Цены на ритуальные услуги в г. {{$city->title}}</div>
            <div class="content_block">{!! $city->text_about_project !!}</div>

        </div>
    </section>
@endif


@include('components.cats-product') 
 
@include('footer.footer') 

<script>
 

$( ".li_cat_children_marketplace" ).on( "click", function() {
    $('.bac_loader').show()
    $('.load_block').show()
    let filters={}
    if($(this).parent('.ul_childern_cats_marketplace').siblings('.li_cat_main_marketplace').attr('id_category')==36){
        $('.marketplace_filters').removeClass('active_filters')
        $('#filter_1').addClass('active_filters')
         filters  = {
            'layering':$('#layering option:checked').val(),
            'cemetery_id':$('#cemetery_id').val(),
            "category": $(this).attr('id_category'),
            'sort': $('.name_sort').attr('val'),
            'material':$('#material option:checked').val(),
            'size':$('#size option:checked').val(),
        };
    }
    if($(this).parent('.ul_childern_cats_marketplace').siblings('.li_cat_main_marketplace').attr('id_category')==45){
        $('.marketplace_filters').removeClass('active_filters')
        $('#filter_3').addClass('active_filters')
         filters  = {
            'district_id':$('#district_id').val(),
            "category": $(this).attr('id_category'),
            'sort': $('.name_sort').attr('val'),
        };
    }
    if($(this).parent('.ul_childern_cats_marketplace').siblings('.li_cat_main_marketplace').attr('id_category')==31){
        $('.marketplace_filters').removeClass('active_filters')
        $('#filter_2').addClass('active_filters')
         filters  = {
            "category": $(this).attr('id_category'),
            'sort': $('.name_sort').attr('val'),
        };
    }


    let category_selected=$(this)
    $.ajax({
        type: 'GET',
        url: '{{route('marketplace.ajax.filters')}}',
        data: filters,
        success: function (result) {
            $( ".li_cat_children_marketplace" ).removeClass('active_category')
            $('.li_cat_main_marketplace').removeClass('active_main_category')
            category_selected.parent().siblings('.li_cat_main_marketplace').addClass('active_main_category')
            category_selected.addClass('active_category')
            $('.html_products_wrap').html(result)
            let strings = [];
            for (const [key, value] of Object.entries(filters)) {
                strings.push(key+"="+value)
            }
            let st = strings.join("&")
            window.history.pushState('marketplace', 'Title', '/{{$city->slug}}/marketplace/'+ category_selected.attr('slug')+'?'+st);
        },
        error: function () {
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
            alert('Ошибка');
        }
    });
    let cat={
        "category": $(this).attr('id_category'),
    }
    $.ajax({
        type: 'GET',
        url: '{{route('marketplace.ajax.cat')}}',
        data:  cat,
        success: function (result) {
            $('.html_faqs_wrap').html(result)
        },
        error: function () {
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
            alert('Ошибка');
        }
    });

    $.ajax({
        type: 'GET',
        url: '{{route('marketplace.ajax.cat.content')}}',
        data:  cat,
        success: function (result) {
            $('.html_cat_content_wrap').html(result)
        },
        error: function () {
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
            alert('Ошибка');
        }
    });

    
    $.ajax({
        type: 'GET',
        url: '{{route('marketplace.ajax.cat.reviews')}}',
        data:  cat,
        success: function (result) {
            $('.html_cat_reviews_wrap').html(result)
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()

            const projectSliders = document.querySelectorAll('.reviews_funeral_agencies_swiper')

            projectSliders && projectSliders.forEach(slider => {
                let projectSlider = new Swiper(slider, {
                    observer: true,
                    observeParents: true,
                    observeSlideChildren: true,
                    spaceBetween: 20,

                    breakpoints: {
                        340: {
                        slidesPerView: 1,
                        },
                        1000: {
                        slidesPerView: 2,
                        },
                        1100: {
                        slidesPerView: 3,
                        },
                        1290: {
                        slidesPerView: 4,
                        },
                    
                    },
                    navigation: {
                        nextEl: ".swiper_button_next_reviews_funeral_agencies",
                        prevEl: ".swiper_button_prev_reviews_funeral_agencies ",
                    },
                    })/* projectSlider */
            }) /* forEach */

        },
        error: function () {
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
            alert('Ошибка');
        }
    });

    $.ajax({
        type: 'GET',
        url: '{{route('marketplace.ajax.cat.manual')}}',
        data:  cat,
        success: function (result) {
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
            $('.html_cat_manual_wrap').html(result)
        },
        error: function () {
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
            alert('Ошибка');
        }
    });

    $.ajax({
        type: 'GET',
        url: '{{route('marketplace.ajax.title')}}',
        data:  filters,
        success: function (result) {
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
            $('.title_page').html(result)
        },
        error: function () {
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
            alert('Ошибка');
        }
    });

    $('.navigation_pages span').html(category_selected.html())



});




$( ".filter_sort .li_sort" ).on( "click", function() {

$('.bac_loader').show()
$('.load_block').show()

$('.name_sort').html($(this).html())
$('.name_sort').attr('val', $(this).attr('val'))
let category_selected=$('.active_category')
let filters={}
    if($('.active_main_category').attr('id_category')==36){
        $('.marketplace_filters').removeClass('active_filters')
        $('#filter_1').addClass('active_filters')
         filters  = {
            'layering':$('#layering option:checked').val(),
            'cemetery_id':$('#cemetery_id').val(),
            "category": $('.active_category').attr('id_category'),
            'sort': $('.name_sort').attr('val'),
            'material':$('#material option:checked').val(),
            'size':$('#size option:checked').val(),
        };
    }
    if($('.active_main_category').attr('id_category')==45){
        $('.marketplace_filters').removeClass('active_filters')
        $('#filter_3').addClass('active_filters')
         filters  = {
            'district_id':$('#district_id').val(),
            "category": $('.active_category').attr('id_category'),
            'sort': $('.name_sort').attr('val'),
        };
    }
    if($('.active_main_category').attr('id_category')==31){
        $('.marketplace_filters').removeClass('active_filters')
        $('#filter_2').addClass('active_filters')
         filters  = {
            "category": $('.active_category').attr('id_category'),
            'sort': $('.name_sort').attr('val'),
        };
    }
    $.ajax({    
        type: 'GET',
        url: '{{route('marketplace.ajax.filters')}}',
        data: filters,
        success: function (result) {
            
            $('.html_products_wrap').html(result)
            let strings = [];
            for (const [key, value] of Object.entries(filters)) {
                strings.push(key+"="+value)
            }

            let st = strings.join("&")
            window.history.pushState('marketplace', 'Title', '/{{$city->slug}}/marketplace/'+ category_selected.attr('slug')+'?'+st);
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


$( "#filter_1 .filter_block select" ).on( "change", function() {
    $('.bac_loader').show()
    $('.load_block').show()
    let category_selected=$('.active_category')

    $(this).addClass('active_select')
    let filters  = {
        'layering':$('#layering option:checked').val(),
        'cemetery_id':$('#cemetery_id').val(),
        'sort':$('#sort option:checked').val(),
        "category": $('.active_category').attr('id_category'),
        'material':$('#material option:checked').val(),
        'size':$('#size option:checked').val(),
    };
    $.ajax({
        type: 'GET',
        url: '{{route('marketplace.ajax.filters')}}',
        data: filters,
        success: function (result) {
            
            $('.html_products_wrap').html(result)
            let strings = [];
            for (const [key, value] of Object.entries(filters)) {
                strings.push(key+"="+value)
            }

            let st = strings.join("&")
            window.history.pushState('marketplace', 'Title', '/{{$city->slug}}/marketplace/'+ category_selected.attr('slug')+'?'+st);
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
        },
        error: function () {
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
            alert('Ошибка');
        }
    });
    
});





$( "#cemetery_id" ).on( "change", function() {
    $('.bac_loader').show()
    $('.load_block').show()
    let category_selected=$('.active_category')

    let title_cemetery=$(this).children('option:checked').html()
    let id_cemetery=$(this).val()

    let filters  = {
        'layering':$('#layering option:checked').val(),
        'cemetery_id':id_cemetery,
        'sort':$('#sort option:checked').val(),
        "category": $('.active_category').attr('id_category'),
        'material':$('#material option:checked').val(),
        'size':$('#size option:checked').val(),
    };
    $.ajax({
        type: 'GET',
        url: '{{route('marketplace.ajax.filters')}}',
        data: filters,
        success: function (result) {
            $('.html_products_wrap').html(result)
            let strings = [];
            for (const [key, value] of Object.entries(filters)) {
                strings.push(key+"="+value)
            }
            let st = strings.join("&")
            window.history.pushState('marketplace', 'Title', '/{{$city->slug}}/marketplace/'+ category_selected.attr('slug')+'?'+st);
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
        url: '{{route('marketplace.ajax.cemetery')}}',
        data: filters,
        success: function (result) {
            $('#cemetery_form').modal('hide')
            $('.html_cemetery_wrap').html(result)
            $('.flex_cemetery_market span').html(title_cemetery)
            $('.flex_cemetery_market span').attr('id_cemetery',id_cemetery)
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
            let href='/cemetery/'+id_cemetery
            $('.index_title .cemetery_title').html(title_cemetery+' кладбище')
            $('.index_title .cemetery_title').attr('href',href)
            
        },
        error: function () {
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
            alert('Ошибка');
        }
    });

    $.ajax({
        type: 'GET',
        url: '{{route('marketplace.ajax.title')}}',
        data:  filters,
        success: function (result) {
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
            $('.title_page').html(result)
        },
        error: function () {
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
            alert('Ошибка');
        }
    });
})
    
$( "#district_id" ).on( "change", function() {
    $('.bac_loader').show()
    $('.load_block').show()
    let category_selected=$('.active_category')

    let filters  = {
        'district_id':$(this).val(),
        'sort':$('#sort option:checked').val(),
        "category": $('.active_category').attr('id_category'),
       
    };
    $.ajax({
        type: 'GET',
        url: '{{route('marketplace.ajax.filters')}}',
        data: filters,
        success: function (result) {
            $('.html_products_wrap').html(result)
            let strings = [];
            for (const [key, value] of Object.entries(filters)) {
                strings.push(key+"="+value)
            }
            let st = strings.join("&")
            window.history.pushState('marketplace', 'Title', '/{{$city->slug}}/marketplace/'+ category_selected.attr('slug')+'?'+st);
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
        url: '{{route('marketplace.ajax.title')}}',
        data:  filters,
        success: function (result) {
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
            $('.title_page').html(result)
        },
        error: function () {
            $('.bac_loader').fadeOut()
            $('.load_block').fadeOut()
            alert('Ошибка');
        }
    });
})
</script>