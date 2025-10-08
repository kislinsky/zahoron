
@extends('account.admin.components.page')

@section('title', '')

@section('content')
<div class="">
    <div class="title_middle">Импорт кладбищ из файла</div>  
    <form class="default_admin_form" method="post" enctype="multipart/form-data" action="{{ route('account.admin.parsing.cemetery') }}">
        @csrf

        <div class="mb-3">
            <label for="cemeteryFiles" class="form-label">Выберите файл</label>
            <input class="form-control" type="file" multiple name="files[]" id="cemeteryFiles">
        </div>

        <div class="block_input">
            <label>Выберите тип загрузки</label>
            <select name="import_type" id="import_type_cemetery" class="form-select">
                <option value="create">Создать новые кладбища</option>
                <option value="update">Обновить кладбища</option>
            </select>  
        </div>

        <div class="block_input">
            <label>Выберите цену для геопозиций</label>
            <input type="text" name='price_geo' >
        </div>

     <div class="block_input filter_update_cemetery" style="display: none;" id="update_fields_block">
        <label>Выбор полей для обновления данных</label>
        <select name="columns_to_update[]" id="columns_to_update" class="form-select" multiple>
            <option value="title">Название</option>
            <option value="slug">Slug</option>
            <option value="adres">Адрес кладбища</option>
            <option value="responsible_person_address">Адрес ответственного лица</option>
            <option value="responsible_organization">Ответственная организация</option>
            <option value="okved">OKVED</option>
            <option value="inn">ИНН</option>
            <option value="city_id">Город</option>
            <option value="width">Широта</option>
            <option value="longitude">Долгота</option>
            <option value="rating">Рейтинг</option>
            <option value="phone">Телефон</option>
            <option value="email">Email</option>
            <option value="img_url">Главное фото</option>
            <option value="galerey">Фотографии</option>
            <option value="time_difference">Разница во времени</option>
            <option value="responsible">Ответственный</option>
            <option value="cadastral_number">Кадастровый номер</option>
            <option value="price_burial_location">Цена места захоронения</option>
            <option value="two_gis_link">Ссылка 2GIS</option>
            <option value="status">Статус</option>
            <option value="date_foundation">Дата основания</option>
            <option value="working_hours">Режим работы</option>
            <option value="address_responsible_person">Адрес ответственного лица</option>
            <option value="responsible_person_full_name">ФИО ответственного лица</option>
        </select>  
    </div>

     

        @error('files')
            <div class='error-text'>{{ $message }}</div>
        @enderror

        <div class="col-auto margin_top_20">
            <button type="submit" class="btn btn-primary mb-3">Начать импорт</button>
        </div>   
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const importTypeSelect = document.getElementById('import_type_cemetery');
        const updateFieldsBlock = document.getElementById('update_fields_block');

        function toggleUpdateFields() {
            if (importTypeSelect.value === 'update') {
                updateFieldsBlock.style.display = 'block';
            } else {
                updateFieldsBlock.style.display = 'none';
            }
        }

        importTypeSelect.addEventListener('change', toggleUpdateFields);

        // Инициализация на загрузке страницы
        toggleUpdateFields();
    });
</script>

    <div class="">
        <div class="title_middle">Добавить новые отзывы о кладбищах из файла</div>  
        <form class='default_admin_form' method="post" enctype="multipart/form-data" action="{{route('account.admin.parsing.cemetery.reviews')}}">
            @csrf
            <div class="mb-3">
                <label for="formFile_2" class="form-label">Выберите файл</label>
                <input class="form-control" type="file" name='file_reviews' id="formFile_2">
            </div>
            @error('file_reviews')
                <div class='error-text'>{{ $message }}</div>
            @enderror
            <div class="col-auto">
                <button type="submit" class="btn btn-primary mb-3">Начать импорт</button>
            </div>   
        </form>
    </div>
@endsection
