@include('header.header')

<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
           {{view('organization.components.catalog.title-page',compact('city','category','category_main','district_choose','cemetery_choose'));}}
        </div>
        <img class='rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
    </div>
</section>


<section class="organization_marketplace">
    <div class="container">
        <div class="grid_product_two">
            <div class="one_block_market">
                <div class="block_table_price_orgniaztions">
                    {{view('organization.components.catalog.prices',compact('price_min','price_middle','price_max','city','category'));}}
                   
                </div>

                {{view('organization.components.catalog.filters',compact('filter_work','sort','cemeteries','cemetery_choose','districts','district_choose'))}}
                
                <div class="ul_organizaiotns">
                    {{view('organization.components.catalog.organizations-show',compact('organizations_category'))}}
                </div>
                

            </div>
            <div class="sidebar">
                {{view('organization.components.catalog.sidebar',compact('cats','category'))}}
            </div>
        </div>
</section>

<div class="map_organizations">
    {{view('organization.components.catalog.map-cats',compact('category','organizations_category','city'));}}
</div>

<section>
    <div class="container">
        <div class="gos_block gos_block_1">
            <img src="{{asset('storage/uploads/image 29.png')}}" alt="">  
            <div class="content_gos_block">
                <div class="title_blue_big">Государственные выплаты <span class='title_green_big'>+ 13500 рублей</span></div>    
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
                <div class="title_text_block">Получите расчет стоимости ритуальных
                    услуг от 10 проверенных организаций 
                    без дополнительных услуг
                </div>
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

{{view('components.news',compact('news'))}}

@include('components.faq') 

{{view('components.news-video',compact('news_video'))}}


@if($city->text_about_project!=null)
    <section class="about_company bac_gray">
        <div class="container">
            <div class="title">О проекте "Цены на ритуальные услуги в г. {{$city->title}}</div>
            <div class="content_block">{!! $city->text_about_project !!}</div>

        </div>
    </section>
@endif

@include('components.cats-organization') 


@if($city->text_how_properly_arrange_funeral_services!=null)
    <section class="about_company bac_gray">
        <div class="container">
            <div class="title">Как правильно оформить ритуальные услуги в г. {{$city->title}}</div>
            <div class="content_block">{!!$city->text_how_properly_arrange_funeral_services !!}</div>

        </div>
    </section>
@endif
@include('footer.footer') 



<script>

$( ".li_cat_children_marketplace" ).on( "click", function() {

    if( $(this).parent('.ul_childern_cats_marketplace').siblings('.li_cat_main_marketplace').attr('id_category')==45){
        $( ".filter_block_organization select" ).removeClass('active_select_filter_organiaztion')
        $('#district_id').addClass('active_select_filter_organiaztion')
        $( ".filter_block_organization select" ).removeClass('active_select_filter_organiaztion_2')
        $('#district_id').addClass('active_select_filter_organiaztion_2')
        $('#label_select_1').hide()
        $('#label_select_2').show()
    }else{
        $( ".filter_block_organization select" ).removeClass('active_select_filter_organiaztion')
        $('#cemetery_id').addClass('active_select_filter_organiaztion')
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
    console.log($('.name_sort').attr('val'))
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
            window.history.pushState('organizations', 'Title', '/{{$city->slug}}/organizations?'+st);
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
    console.log(filters)
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
            window.history.pushState('organizations', 'Title', '/{{$city->slug}}/organizations?'+st);
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




$( ".filter_sort .li_sort" ).on( "click", function() {

        $('.bac_loader').show()
        $('.load_block').show()

        let cemetery_id=null;
        let district_id=null;

        if($('.active_select_filter_organiaztion').attr('name')=='cemetery_id'){
            district_id=null;
            cemetery_id=$('.active_select_filter_organiaztion').val()
        }

        $('.name_sort').html($(this).html())
        $('.name_sort').attr('val', $(this).attr('val'))

        let filter_work='off';
        if($('#filter_work').is(':checked')==true){
            filter_work='on';
        }


        let filters  = {
            'filter_work':filter_work,
            'district_id':district_id,
            'cemetery_id':cemetery_id,
            'sort':$(this).attr('val'),
            "category_id": $('.active_category').attr('id_category'),
        };
        console.log(filters)
        $.ajax({
            type: 'GET',
            url: '{{route('organizations.ajax.filters')}}',
            data: filters,
            success: function (result) {
                $('.ul_organizaiotns').html(result)
                let strings = [];
                for (const [key, value] of Object.entries(filters)) {
                    strings.push(key+"="+value)
                }
                let st = strings.join("&")
                window.history.pushState('organizations', 'Title', '/{{$city->slug}}/organizations?'+st);
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
    let cemetery_id=null;
    let district_id=null;
    let filters={
        'filter_work':filter_work,
        'cemetery_id':cemetery_id,
        'district_id':district_id,
        'sort': $('.name_sort').attr('val'),
        "category_id": $('.active_category').attr('id_category'),
    }
    if($('.active_select_filter_organiaztion').attr('name')=='cemetery_id'){
        district_id=null;
        cemetery_id=$('.active_select_filter_organiaztion').val()
        filters  = {
            'filter_work':filter_work,
            'cemetery_id':cemetery_id,
            'sort': $('.name_sort').attr('val'),
            "category_id": $('.active_category').attr('id_category'),
        };
    }

    if($('.active_select_filter_organiaztion').attr('name')=='district_id'){
        district_id=$('.active_select_filter_organiaztion').val();
        cemetery_id=null
        filters  = {
            'filter_work':filter_work,
            'district_id':district_id,
            'sort': $('.name_sort').attr('val'),
            "category_id": $('.active_category').attr('id_category'),
        };
    }
   
   
    $.ajax({
        type: 'GET',
        url: '{{route('organizations.ajax.filters')}}',
        data: filters,
        success: function (result) {
            $('.ul_organizaiotns').html(result)
            let strings = [];
            for (const [key, value] of Object.entries(filters)) {
                strings.push(key+"="+value)
            }
            let st = strings.join("&")
            window.history.pushState('organizations', 'Title', '/{{$city->slug}}/organizations?'+st);
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
</script>