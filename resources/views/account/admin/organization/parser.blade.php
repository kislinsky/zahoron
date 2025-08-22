

@extends('account.admin.components.page')

@section('title', '')

@section('content')
    <div class="">
        <div class="title_middle">Добавить новые организации из файла</div>  
        <form class='default_admin_form' method="post" enctype="multipart/form-data" action="{{route('account.admin.parsing.organization')}}">
            @csrf
            <div class="mb-3">
                <label for="formFile" class="form-label">Выберите файл</label>
                <input class="form-control" type="file" multiple name='files[]' id="formFile">
            </div>

            <div class="block_input">
                <label >Выберите тип загрузки</label>
                <select name="import_type" id="">
                    <option value="new">Создать новые организации</option>
                    <option value="update">Обновить организации</option>
                </select>  
            </div>
            <div class="block_input">
                <label >Прикрепленные организации импортировать</label>
                <select name="import_with_user" id="">
                    <option value="0">Нет</option>
                    <option value="1">Да</option>
                </select>  
            </div>
            <div class="block_input filter_update_organization">
                <label >Выбор полей для обновления данных</label>
                <select name="columns_to_update[]" id="" multiple>
                    <option value="title">Название организации</option>
                    <option value="link_website">Сайт организации</option>
                    <option value="address">Адрес</option>
                    <option value="coordinates">Координаты</option>
                    <option value="phone">телефон</option>
                    <option value="logo">Логотип</option>
                    <option value="main_photo">Главное фото</option>
                    <option value="working_hours">Режим работы</option>
                    <option value="gallery">Галлерея</option>
                    <option value="services">Виды услуг</option>
                    <option value="inn">Инн</option>

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
    
    <div class="">
        <div class="title_middle">Добавить новые отзывы о организациях из файла</div>  
        <form class='default_admin_form' method="post" enctype="multipart/form-data" action="{{route('account.admin.parsing.organization.reviews')}}">
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
    
  
    <div class="title_middle">Добавить новые цены для организаций из файла</div>  

    <form class='default_admin_form' action="{{ route('account.admin.parsing.organization.prices') }}" method="POST" enctype="multipart/form-data">
        
        @csrf      
            <div class="mb-3">
                <label for="formFile_2" class="form-label">Выберите файл</label>
                <input type="file" name="files_prices[]" id="files" multiple class="form-control" accept=".xlsx,.xls,.csv" required>
            </div>
            @error('files')
                <div class='error-text'>{{ $message }}</div>
            @enderror
            
           
            <div class="block_input">
                <label >Прикрепленные организации импортировать</label>
                <select name="import_with_user_prices" id="">
                    <option value="0">Нет</option>
                    <option value="1">Да</option>
                </select>  
            </div>

            <div class="block_input">
                <label >Заменять пустые цены на "уточняйте"</label>
                <select name="update_empty_to_ask" id="">
                    <option value="1">Да</option>
                    <option value="0">Нет</option>
                </select>  
            </div>
            <button type="submit" class="btn btn-primary margin_top_20">Импортировать цены</button>

        </form>

<script>
    $('select[name="import_type"]').on( "change", function() {
        val=$(this).children('option:checked').val()
        if(val=='update'){
            $( ".filter_update_organization" ).show()
        }else{
            $( ".filter_update_organization" ).hide()
        }
    })
</script>

@endsection


