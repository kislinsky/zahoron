@extends('account.agency.components.page')
@section('title', "Редактирование товара")

@section('content')
<form method='post' enctype='multipart/form-data' class="block_add_product" action="{{ route('account.agency.update.product', $product->id) }}">
    @csrf
    @method('PUT')

    {{view('account.agency.components.product.cats-add',compact('categories','categories_children','category_choose_main','category_choose_children'))}}

    {{-- Название --}}
    <div class="block_input">
        <div class="text_middle_index">Название</div>
        <div class="block_input">
            <div class='text_gray'>Введите название товара, не более 120 символов</div>
            <input placeholder='Товар' maxlength=120 type="text" name='title' 
                   value="{{ old('title', $product->title) }}">
            @error('title')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Описание --}}
    <div class="block_input">
        <div class="text_middle_index">Описание</div>
        <div class="block_input">
            <div class='text_gray'>Введите описание товара, не более 1000 символов</div>
            <textarea name="content" maxlength=1000 cols="30" rows="10" 
                      placeholder="Описание">{{ old('content', $product->content) }}</textarea>
            @error('content')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Фотографии --}}
    {{view('account.agency.components.product.add-uploads',compact('images'))}}


    {{-- Стоимость --}}
    <div class="block_input">
        <div class="block_input">
            <div class="text_middle_index">Стоимость</div>
            <div class="block_input">
                <div class='text_gray'>Стоимость товара</div>
                <input placeholder='110 000 ₽' min=1 type="number" name='price' 
                       value="{{ old('price', $product->price) }}">
                @error('price')
                    <div class='error-text'>{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="block_input">
            <div class="block_input">
                <div class='text_gray'>Стоимость товара со скидкой* (необязательное поле)</div>
                <input placeholder='100 000 ₽' min=1 type="number" name='price_sale' 
                       value="{{ old('price_sale', $product->price_sale) }}">
            </div>
        </div>
    </div>

    {{-- Дополнительные блоки (будут показаны в зависимости от категории) --}}
    <div class="block_additionals block_beatification" style="display: none;">
        <div class="block_input">
            <div class="text_middle_index">Дополнительное описание</div>
            <div class="block_input">
                <div class='text_gray'>Выберите материал</div>
                <div class="select">
                    <select name="material" id="">
                        <option value="Гранит" {{ old('material', $product->material) == 'Гранит' ? 'selected' : '' }}>Гранит</option>
                        <option value="Мрамор" {{ old('material', $product->material) == 'Мрамор' ? 'selected' : '' }}>Мрамор</option>
                    </select>
                </div>
            </div>
            <div class="block_input">
                <div class='text_gray'>Размер</div>
                <div class="select">
                    <select name="size" id="">
                        <option value="100x55x5" {{ old('size', $product->size) == '100x55x5' ? 'selected' : '' }}>100x55x5</option>
                        <option value="101x55x5" {{ old('size', $product->size) == '101x55x5' ? 'selected' : '' }}>101x55x5</option>
                    </select>
                </div>
            </div>
            <div class="block_input">
                <div class='text_gray'>Свой размер</div>
                <input type="text" name="your_size" id="" placeholder="100x55x5" 
                       value="{{ old('your_size', $product->size ? explode('|', $product->size)[1] ?? '' : '') }}">
            </div>
        </div>
    </div>

    <div class="block_additionals block_funeral_service" style="display: none;">
        <div class="block_input">
            <div class="text_middle_index">Что входит?</div>
            <div class="flex_additional_product">
                <input type="text" name='parameters' placeholder='Характеристика1|Характеристика2'
                      value="{{ old('parameters', $product->parameters ? $product->parameters->pluck('title')->implode('|') : '') }}">
            </div>
        </div>
    </div>

    <div class="block_additionals block_organization_commemorations" style="display: none;">
        <div class="flex_input_form_contacts">
            <div class="block_input">
                <div class="block_input">
                    <div class="text_middle_index">Расположение кафе</div>
                    <div class="block_input">
                        <div class='text_gray'>Ширина</div>
                        <input placeholder='11.111.11' type="text" name='width' 
                               value="{{ old('width', $product->location_width) }}">
                    </div>
                </div>
                <div class="block_input">
                    <div class="block_input">
                        <div class='text_gray'>Долгота</div>
                        <input placeholder='11.111.11' type="text" name='longitude' 
                               value="{{ old('longitude', $product->location_longitude) }}">
                    </div>
                </div>
               <div class="block_input">
                    <div class="text_middle_index">Меню</div>
                    <input type="text" name='menus'
                        placeholder='Характеристика1:количество или грамовки|Характеристика2:количество или грамовки'
                        value="{{ old('menus', $product->menus?->map(function($menu) {
                            return $menu->title . ':' . $menu->content;
                        })->implode('|') ?? '') }}">
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="blue_btn settings_margin_form">Обновить товар</button>
</form>

<script>
$(document).ready(function() {
    // Определяем текущий тип категории для показа нужного блока
    var categoryId = {{ $product->category_id }};
    var parentCategoryId = {{ $product->category_parent_id }};
    
    // Функция для получения типа категории
    function getCategoryType(categoryId) {
        // Здесь нужно сделать AJAX запрос для получения типа категории
        // или передать эту информацию из контроллера
        // Временно используем data-атрибуты
        return $('#children-category option[value="' + categoryId + '"]').data('type');
    }
    
    // Показываем соответствующий блок при загрузке
    setTimeout(function() {
        var type = getCategoryType(categoryId);
        if (type === 'beatification') {
            $('.block_beatification').show();
        } else if (type === 'funeral-service') {
            $('.block_funeral_service').show();
        } else if (type === 'organization-commemorations') {
            $('.block_organization_commemorations').show();
        }
    }, 100);

    

    
});


</script>

@endsection