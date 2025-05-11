
@extends('account.admin.components.page')

@section('title', '')

@section('content')

    <div class="">
        <div class="title_middle">Импорт географии из файла</div>  
        <form class="default_admin_form" method="post" enctype="multipart/form-data" action="{{ route('account.admin.parsing.geo') }}">
            @csrf

            <div class="mb-3">
                <label for="cemeteryFiles" class="form-label">Выберите файлы</label>
                <input class="form-control" type="file" multiple name="files[]" id="cemeteryFiles">
            </div>

            @error('files[]')
                <div class='error-text'>{{ $message }}</div>
            @enderror
            @foreach ($errors->all() as $error)
            <div class="alert alert-danger">{{ $error }}</div>
        @endforeach
            <div class="col-auto margin_top_20">
                <button type="submit" class="btn btn-primary mb-3">Начать импорт</button>
            </div>   
        </form>
    </div>
@endsection
