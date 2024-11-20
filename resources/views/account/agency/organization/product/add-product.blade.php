@extends('account.agency.components.page')
@section('title', "Создайте свой товар")

@section('content')
<form method='post' enctype='multipart/form-data'  class="block_add_product" action="{{route('account.agency.create.product')}}">
    
    @csrf

    {{view('account.agency.components.product.cats-add',compact('categories','categories_children'))}}

    <div class="block_input">
        <div class="text_middle_index">Название</div>
        <div class="block_input">
            <div class='text_gray' for="">Введите название товара, не более 120 символов</div>
            <input placeholder='Товар' maxlength=120 type="text" name='title' >
        </div>
    </div>
    <div class="block_input">
        <div class="text_middle_index">Описание</div>
        <div class="block_input">
            <div class='text_gray'  for="">Введите описание товара, не более 1000 символов</div>
        <textarea name="content" id="" maxlength=1000 cols="30" rows="10" placeholder="Описание"></textarea>
        </div>
    </div>

    {{view('account.agency.components.product.add-uploads')}}

    <div class="block_input">
        <div class="block_input">
            <div class="text_middle_index">Стоимость</div>
            <div class="block_input">
                <div class='text_gray' for="">Стоимость товара</div>
                <input placeholder='110 000 ₽' min=1  type="number" name='price' >
            </div>
        </div>
        <div class="block_input">
            <div class="block_input">
                <div class='text_gray'  for="">Стоимость товара со скидкой* (необязательное поле)</div>
                <input placeholder='100 000 ₽' min=1  type="numbermin=1 " name='price_sale' >
            </div>
        </div>
    </div>

    <div class="block_additionals block_beatification">
        {{view('account.agency.components.product.additional-features-beatification')}}
    </div>

    <div class="block_additionals block_funeral_service">
        {{view('account.agency.components.product.additional-features-funeral-service')}}
    </div>

    <div class="block_additionals block_organization_commemorations">
        {{view('account.agency.components.product.additional-beatification')}}        
    </div>

    <button class="blue_btn settings_margin_form">Сохранить товар</button>
</form>


<script>
    $('.block_additionals').hide()
    $('.block_funeral_service').show()
</script>

@endsection

