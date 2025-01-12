@extends('account.admin.components.page')

@section('title', "Изменение SEO")

@section('content')


    <div class="">
        <div class="title_middle">{{ $object_columns->first()->title }}</div>  
        <form class='default_admin_form' method="post" enctype="multipart/form-data" action="{{route('account.admin.seo.object.update',$object_columns->first()->page)}}">
            @csrf
            @foreach ($object_columns as $object_column)
                <div class="mb-3">
                    <label for="formFile_2" class="form-label">{{ $object_column->name }}</label>
                    <input class="form-control" type="text"  name='{{ $object_column->name }}' value='{{ $object_column->content }}'>
                </div>
                @error( $object_column->name )
                    <div class='error-text'>{{ $message }}</div>
                @enderror
            @endforeach


            <div class="col-auto">
                <button type="submit" class="btn btn-primary mb-3">Обновить</button>
            </div>   
        </form>
    </div>
  

@endsection