<div class="block_input">
    <div class="title_middle">Услуги</div>
    <div class="text_black"> Какие ритуальные услуги вы осуществляете? </div>
</div>

<div class="block_input">
    <div class="title_middle">Категория</div>
    <div class="select">
        <select name="cat" >
            @foreach ($categories as $categories_one)
                <option value="{{$categories_one->id}}">{{$categories_one->title}}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="block_input">
    <div class="title_middle">Подкатегория</div>
    <div class="add_cat_to_organization">
        <div class="select">
            <select name="cat_children" >
                @foreach ($categories_children as $categories_one)
                    <option value="{{$categories_one->id}}">{{$categories_one->title}}</option>
                @endforeach
            </select>
        </div>
        <img src="{{ asset('storage/uploads/Закрыть.svg') }}" class="btn_add_children_cat">
    </div>
    
    <div class="ul_info_settings_organization cats_organization">
        @foreach ($categories_organization as $category_organization)
            <div class="li_cemetery_agent">
                <div class="mini_flex_li_product">
                    <input type="hidden" value='{{ $category_organization->categoryProduct->id }}'name="categories_organization[]">
                    <div class="title_label">{{ $category_organization->categoryProduct->title ?? 'Default Title' }}</div>
                </div>
                <div  class="delete_cart delete_cat_organization"><img src="{{asset('storage/uploads/Закрыть (1).svg')}}" alt=""></div>
            </div>
        @endforeach
    </div>
    
</div>


<div class="block_inpit_form_search">
    <div class="title_middle">Дополнительные условия</div>
    
    <label class='flex_input_checkbox '>
        <label class="switch">
            <input type="checkbox" name='available_installments' value='1' <?php if($organization->available_installments=='1'){ echo'checked';}?>>
            <span class="slider"></span>
        </label>
        Доступно в рассрочку
    </label>
    <label class='flex_input_checkbox '>
        <label class="switch">
            <input type="checkbox" name='found_cheaper' value='1' <?php if($organization->found_cheaper=='1'){ echo'checked';}?>>
            <span class="slider"></span>
        </label>
        Нашли дешевле снизим цену
    </label>
    <label class='flex_input_checkbox '>
        <label class="switch">
            <input type="checkbox" name='сonclusion_contract' value='1' <?php if($organization->сonclusion_contract=='1'){ echo'checked';}?>>
            <span class="slider"></span>
        </label>
        Заключение договора
    </label>
    <label class='flex_input_checkbox '>
        <label class="switch">
            <input type="checkbox" name='state_compensation' value='1' <?php if($organization->state_compensation=='1'){ echo'checked';}?>>
            <span class="slider"></span>
        </label>
        Государственная компенсация
    </label>
</div>

<div class="ul_info_settings_organization ul_price_organizations_settings">
    @foreach($categories_organization as $category_organization)
        <div class="block_input">
            <label for="">{{  $category_organization->categoryProduct->title ?? 'Default Title' }}</label>
            <input type="text" name='price_cats_organization[]' value='{{$category_organization->price}}'>
        </div>
    @endforeach
</div>


<script>
     $('.btn_add_children_cat').on('click',function() {
        let title_cat=$(this).siblings('.select').children('select').children('option:checked').html()
        let id=$(this).siblings('.select').children('select').children('option:checked').val()
        $('.ul_price_organizations_settings').append("<div class='block_input'><label >"+title_cat+"</label><input type='text' name='price_cats_organization[]' ></div>")
        $('.cats_organization').append("<div class='li_cemetery_agent'><div class='mini_flex_li_product'><input type='hidden' value='"+ id +"' name='categories_organization[]'><div class='title_label'>"+ title_cat +"</div></div><div  class='delete_cart delete_organization'><img src='{{asset('storage/uploads/Закрыть (1).svg')}}'' ></div></div>")
    
    })
    $('.delete_cat_organization').on('click',function() {
        let title_cat=$(this).siblings('.mini_flex_li_product').children('.title_label').html()
        $('.ul_price_organizations_settings .block_input').each(function(index, element) {
            if($(element).children('label').html()==title_cat){
                $(element).remove()
            }
        });
        $(this).parent('.li_cemetery_agent').remove()
    })
    

    $('select[name="cat"]').on( "change", function() {
        let cat_id=$(this).children('option:checked').val()
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
    })
</script>