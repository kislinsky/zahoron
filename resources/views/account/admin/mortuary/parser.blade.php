@extends('account.admin.components.page')

@section('title', '')

@section('content')

    <div class="">
        <div class="title_middle">Добавить новые морги из файла</div>  
        <form class='default_admin_form' method="post" enctype="multipart/form-data" action="{{route('account.admin.parsing.mortuary')}}">
            @csrf
            <div class="mb-3">
                <label for="formFile" class="form-label">Выберите файл</label>
                <input class="form-control" type="file" name='file' id="formFile">
            </div>
            @error('name')
                <div class='error-text'>{{ $message }}</div>
            @enderror
            <div class="col-auto">
                <button type="submit" class="btn btn-primary mb-3">Начать импорт</button>
            </div>   
        </form>
    </div>
    
    <div class="">
        <div class="title_middle">Добавить новые отзывы о моргах из файла</div>  
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
