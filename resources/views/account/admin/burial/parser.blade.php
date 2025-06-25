
@extends('account.admin.components.page')

@section('title', '')

@section('content')
    <div class="">
        <div class="title_middle">Добавить новые захоронения из файла</div>  
        <form class='default_admin_form' method="post" enctype="multipart/form-data" action="{{route('account.admin.burial.import')}}">
            @csrf
            <div class="mb-3">
                <label for="formFile" class="form-label">Выберите файл</label>
                <input class="form-control" type="file" multiple name='files[]' id="formFile">
            </div>
            @error('files[]')
                <div class='error-text'>{{ $message }}</div>
            @enderror
            <div class="col-auto">
                <button type="submit" class="btn btn-primary mb-3">Начать импорт</button>
            </div>   
        </form>
    </div>
@endsection
