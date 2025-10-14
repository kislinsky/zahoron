
@extends('account.admin.components.page')

@section('title', '')

@section('content')
@include('forms.location-2')

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
            <div class="block_input input_location_flex">
        <div class="input_location_settings">
            <div class="input_location">
                <input type="hidden" name="id_cemetery" class='cemetery_id_input'>
                <img  data-bs-toggle="modal" data-bs-target="#location_form_2" class='open_location' src="{{ asset('storage/uploads/Закрыть.svg') }}" alt="">
                <input type="text" name='location_cemetery' placeholder='Расположение' disabled>
            </div>
            
        </div>

    </div> 
        </form>
    </div>
@endsection
