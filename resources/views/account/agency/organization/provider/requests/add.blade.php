@extends('account.agency.components.page')
@section('title', "Заявки стоимости товаров Поставщикам")

@section('content')
<form action='{{route('account.agency.provider.requests.products.create')}}' class="block_add_request_product_to_provider">
    @csrf
    <div class="text_black ">{{get_acf(8,'content')}}</div>

    <div class="block_input">
        <div class="flex_label">
            <div class="title_middle">Выберите ТК</div>
            <label class='flex_input_checkbox checkbox'><input type="checkbox" name='all_lcs'>Любая</label>
        </div>
        <div class="ul_request_to_provider ul_lcs">
           
        </div>
    </div>
    
    <div class="flex_input_form_contacts">
        <div class="block_input">
            <div class="select select_100">
                <select name="lc_select" id="">
                    <option value="Транспортная компания Энергия">Транспортная компания Энергия</option>
                    <option value="ЖелДорЭкспедиция">ЖелДорЭкспедиция</option>
                </select>
            </div>
        </div>
        <div class="blue_btn add_lc">Добавить</div>
    </div>
   

<div class="block_input">
    <div class="title_middle">Выберите товар</div>
    <div class="ul_request_to_provider ul_request_to_product_provider">
           
    </div>
    <div class="flex_input_form_contacts">   
        <div class="block_input">
            <div class="select select_100">
                <select name="products_select" id="">
                    @foreach ($categories_products_provider as $category_product_provider)
                        <option value="{{$category_product_provider->id}}">{{$category_product_provider->title}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="blue_btn add_request_to_product_provider">Добавить</div>
    </div>
</div>
   
    
    <button class='blue_btn max_width_200'>Отправить</button>
</form>
    
        
<script>
    $('.add_lc ').on( "click", function() {
        let title_lc=$(this).siblings('.block_input').children('.select').children('select[name="lc_select"]').children('option:checked').val()
        $('.ul_lcs').append("<div class='li_request_to_provider'><div onclick='$(this).parent().remove()' class='delete_cart delete_li_request_to_provider'><img src='/storage/uploads/Закрыть_blue.svg' ></div><div class='mini_flex_li_product'><input type='hidden' value='"+title_lc+"'name='lcs[]''><div class='title_label'>"+title_lc+"</div></div></div>")
    })


    $('.add_request_to_product_provider ').on( "click", function() {
        let title_product=$(this).siblings('.block_input').children('.select').children('select[name="products_select"]').children('option:checked').html()
        let id_product=$(this).siblings('.block_input').children('.select').children('select[name="products_select"]').children('option:checked').val()
        $('.ul_request_to_product_provider').append("<div class='grid_two'><div class='li_request_to_provider'><div onclick='$(this).parent().parent().remove()' class='delete_cart delete_li_request_to_provider'><img src='/storage/uploads/Закрыть_blue.svg' ></div><div class='mini_flex_li_product'><input type='hidden' value='"+id_product+"'name='products[]''><div class='title_label'>"+title_product+"</div></div></div><div class='block_input'><input name='count[]' type='number' min=1></div>   </div>")
    })
</script>

@endsection
