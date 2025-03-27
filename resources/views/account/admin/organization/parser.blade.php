

@extends('account.admin.components.page')

@section('title', '')

@section('content')
    <div class="">
        <div class="title_middle">Добавить новые организации из файла</div>  
        <form class='default_admin_form' method="post" enctype="multipart/form-data" action="{{route('account.admin.parsing.organization')}}">
            @csrf
            <div class="mb-3">
                <label for="formFile" class="form-label">Выберите файл</label>
                <input class="form-control" type="file" name='file' id="formFile">
            </div>

            <div class="block_input">
                <label >Выберите тип загрузки</label>
                <select name="import_type" id="">
                    <option value="new">Создать новые организации</option>
                    <option value="update">Обновить организации</option>
                </select>  
          </div>
            @error('file')
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
    
    
    <div class="">
        <div class="title_middle">Добавить новые цены для организаций из файла</div>  
        <form class='default_admin_form' method="post" enctype="multipart/form-data" action="{{route('account.admin.parsing.organization.prices')}}">
            @csrf
            <div class="mb-3">
                <label for="formFile_3" class="form-label">Выберите файл</label>
                <input class="form-control" type="file" name='file_prices' id="formFile_3">
            </div>
            @error('file_prices')
                <div class='error-text'>{{ $message }}</div>
            @enderror
            <div class="col-auto">
                <button type="submit" class="btn btn-primary mb-3">Начать импорт</button>
            </div>   
        </form>
    </div>       
@endsection


