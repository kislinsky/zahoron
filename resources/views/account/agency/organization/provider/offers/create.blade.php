@extends('account.agency.components.page')
@section('title', "Создание запроса товара")

@section('content')

    <div class="flex_btn margin_top_down_20">
        <a  href="{{route('account.agency.provider.offer.add')}}">
            <img class='img_width_50' src="{{ asset('storage/uploads/Закрыть.svg')}}" alt="">
        </a>
        <a href='{{route('account.agency.provider.offer.created')}}' class="gray_btn">Созданные</a>
        <a href='{{route('account.agency.provider.offer.answers')}}' class="gray_btn">Ответы</a>
    </div>

    <form method='post' action='{{route('account.agency.provider.offer.create')}}' enctype='multipart/form-data'  class="block_add_product" >
        @csrf
        <div class="block_input">
            <div class="flex_input"><div class="text_middle_index">Категория</div> <label class='flex_input_checkbox checkbox'><input type="checkbox" name='none_category'>нет категории</label></div>
            <div class="select">
              <select name="category" id="">
                @foreach ($categories_products_provider as $category_product_provider)
                    <option value="{{$category_product_provider->id}}">{{$category_product_provider->title}}</option>     
                @endforeach
              </select>
                @error('category')
                    <div class='error-text'>{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="block_input">
            <div class="text_middle_index">Название</div>
            <div class="block_input">
                <div class='text_gray' for="">Введите название товара, не более 120 символов</div>
                <input placeholder='Товар' maxlength=120 type="text" name='title' >
                @error('title')
                    <div class='error-text'>{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="block_input">
            <div class="text_middle_index">Полное описание</div>
            <div class="block_input">
                <div class='text_gray'  for="">Введите описание товара, не более 1000 символов</div>
            <textarea name="content" id="" maxlength=1000 cols="30" rows="10" placeholder="Описание"></textarea>
            @error('content')
                <div class='error-text'>{{ $message }}</div>
            @enderror
            </div>
        </div>


        {{view('account.agency.components.product.add-uploads')}}

    <div class='block_input'>
        <label class='flex_input_checkbox checkbox active_checkbox'><input type="checkbox" checked name='delivery'>Рассчитать доставку</label>
    </div>

        <button class="blue_btn max_width_200">Отправить</button>
    </form>

@endsection