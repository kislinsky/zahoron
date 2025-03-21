@extends('account.agency.components.page')
@section('title', 'Настройки организации')

@section('content')
@include('forms.location-2')

    <form enctype='multipart/form-data' action="{{route('account.agency.organization.settings.update')}}" method='post' class="update_settings_organization">
        @csrf
        <input type="hidden" name="id" value={{$organization->id}}>
        <div class="block_input">
            <label for="">Название</label>
            <input type="text" name="title" value='{{$organization->title}}'>
            @error('title')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>
       

        <div class="block_input">
            <div class="mb-3">
                <label for="formFile" class="form-label">Измените изображение для организации</label>
                <input class="form-control" type="file" name='img_main' >
            </div>
            @error('img_main')
                <div class='error-text'>{{ $message }}</div>
            @enderror
            
            <img class='logo_org_setting' src="{{ $organization->urlImgMain() }}" alt="">

        </div>

        <div class="block_input">
            <div class="mb-3">
                <label for="formFile" class="form-label">Измените логотип для организации</label>
                <input class="form-control" type="file" name='img' >
            </div>
            @error('img')
                <div class='error-text'>{{ $message }}</div>
            @enderror
            
            <img class='logo_org_setting' src="{{ $organization->urlImg() }}" alt="">

        </div>



        {{view('account.agency.components.settings-organization.galerey',compact('organization'))}}


        <div class="block_input">
            <label for="">Обширное описание</label>
            <textarea placeholder="Заполните обширное описание агентства, адрес, какие услуги осуществляете, чтобы мы автоматически сгенерировали хорошее описание вашей фирмы." name="content" >{!!$organization->content!!}</textarea>
             @error('content')
                <div class='error-text'>{{ $message }}</div>
            @enderror
        </div>

        

        

        {{view('account.agency.components.settings-organization.cats',compact('categories','categories_children','categories_organization','organization'))}}
        
        {{view('account.agency.components.settings-organization.contacts',compact('cities','organization'))}}

        {{view('account.agency.components.settings-organization.work-time',compact('cities','organization','days'))}}

        
        {{view('account.agency.components.settings-organization.cemeteries',compact('cemeteries'))}}
        

        

        <button type="submit" class='blue_btn'>Сохранить настройки</button>
    </form>
    
@endsection


