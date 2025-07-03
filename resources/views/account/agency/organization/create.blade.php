@extends('account.agency.components.page')
@section('title', 'Создание организации')

@section('content')
@include('forms.location-2')

    <form enctype='multipart/form-data' action="{{route('account.agency.organization.create')}}" method='post' class="update_settings_organization">
        @csrf
        

        <div class="block_input">
            <label for="">Название</label>
            <input type="text" name="title">
            @error('title')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>
       

        <div class="block_input">
            <div class="mb-3">
                <label for="formFile" class="form-label">Добавьте изображение для организации</label>
                <input class="form-control" type="file" name='img_main' >
            </div>
            @error('img_main')
            <div class='error-text'>{{ $message }}</div>
        @enderror
        </div>

        <div class="block_input">
            <div class="mb-3">
                <label for="formFile" class="form-label">Добавьте логотип для организации</label>
                <input class="form-control" type="file" name='img' >
            </div>
            @error('img')
            <div class='error-text'>{{ $message }}</div>
        @enderror
        </div>

      

        <div class="block_input">
            <label for="">Обширное описание</label>
            <textarea placeholder="Заполните обширное описание агентства, адрес, какие услуги осуществляете, чтобы мы автоматически сгенерировали хорошее описание вашей фирмы." name="content" ></textarea>
             @error('content')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>

        
        {{view('account.agency.components.create-organization.cats',compact('categories','categories_children'))}}
        
        {{view('account.agency.components.create-organization.contacts')}}

        {{view('account.agency.components.create-organization.work-time')}}

        
        {{view('account.agency.components.create-organization.cemeteries',compact('cemeteries'))}}
        

        {{view('account.agency.components.product.add-uploads')}}

       
        <button type="submit" class='blue_btn'>Отправить на модерацию</button>
    </form>
    
@endsection


