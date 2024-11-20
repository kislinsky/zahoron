<div class="flex_inputs_filters_organiaztions">
    <div class="input_search_products_organizations">
        <input name='s' type="text" placeholder="Поиск объявления">
        <img src="{{asset('storage/uploads/ic_outline-keyboard-arrow-down.svg')}}" alt="">
    </div>
    <div class="block_input">
        <div class="select">
            <select name="cat" >
                @foreach ($categories as $categories_one)
                    <option type='{{$categories_one->type}}' value="{{$categories_one->id}}">{{$categories_one->title}}</option>
                @endforeach
            </select>
        </div>
    </div>
    
    <div class="block_input">
        <div class="select">
            <select name="cat_children" >
                <option value="about">Подкатегория обьявления</option>
                @foreach ($categories_children as $categories_one)
                    <option value="{{$categories_one->id}}">{{$categories_one->title}}</option>
                @endforeach
            </select>
        </div>
    </div>
    
</div>




<script>
$( ".input_search_products_organizations input" ).on( "input", function() {
   let s=$(this).val()
   let filters  = {
        's':s,
    }; 
    $.ajax({
        type: 'GET',
        url: '{{ route("account.agency.search.product") }}',
        data: {
            "_token": "{{ csrf_token() }}",
            's': s,
        }, success: function (result) {
           $('.show_organizations_products_block').html(result)
            let strings = [];
                for (const [key, value] of Object.entries(filters)) {
                    strings.push(key+"="+value)
                }
                let st = strings.join("&")
                window.history.pushState('organizations', 'Title', '/{{$city->slug}}/account/agency/products?'+st);
        },
        error: function () {
           
            alert('Ошибка');
        }
    });

});


$('select[name="cat"]').on( "change", function() {
    let type=$(this).children('option:checked').attr('type')
    let cat_id=$(this).children('option:checked').val()

    let filters  = {
        'cat_id':cat_id,
        'parent_category_id': cat_id,
    }; 


    $.ajax({
        type: 'GET',
        url: '{{ route("category.product.children.ul") }}',
        data: {
            "_token": "{{ csrf_token() }}",
            'cat_id': cat_id,
        }, success: function (result) {
            $('select[name="cat_children"]').html(result)
        },
        error: function () {
            alert('Ошибка');
        }
    });
    $.ajax({
        type: 'GET',
        url: '{{ route("account.agency.filters.product") }}',
        data: {
            "_token": "{{ csrf_token() }}",
            'parent_category_id': cat_id,
        }, success: function (result) {
            $('.show_organizations_products_block').html(result)
             let strings = [];
                for (const [key, value] of Object.entries(filters)) {
                    strings.push(key+"="+value)
                }
                let st = strings.join("&")
                window.history.pushState('organizations', 'Title', '/{{$city->slug}}/account/agency/products?'+st);
        },
        error: function () {
            alert('Ошибка');
        }
    });
})

$('select[name="cat_children"]').on( "change", function() {
    let category_id=$(this).children('option:checked').val()

    let filters  = {
        'category_id':category_id,
    }; 

    $.ajax({
        type: 'GET',
        url: '{{ route("account.agency.filters.product") }}',
        data: {
            "_token": "{{ csrf_token() }}",
            'category_id': category_id,
        }, success: function (result) {
            $('.show_organizations_products_block').html(result)
             let strings = [];
                for (const [key, value] of Object.entries(filters)) {
                    strings.push(key+"="+value)
                }
                let st = strings.join("&")
                window.history.pushState('organizations', 'Title', '/{{$city->slug}}/account/agency/products?'+st);
        },
        error: function () {
            alert('Ошибка');
        }
    });
})
</script>