@extends('account.agency.components.page')
@section('title', 'Настройки пользователя')

@section('content')

    <form action='{{ route('account.organization.settings.update') }}' method='post' enctype='multipart/form-data' class="form_settings">
    @csrf
    
    {{view('account.agency.components.settings-user.main-info-ep',compact('user'))}}

    {{view('account.agency.components.settings-user.legal-address',compact('user','edges','cities'))}}

    {{view('account.agency.components.settings-user.contacts',compact('user'))}}
     
    {{view('account.agency.components.settings-user.notification',compact('user'))}}

    {{view('account.agency.components.settings-user.others',compact('user'))}}
   
    <button type="submit" class='blue_btn settings_margin_form'>Сохранить настройки</button>
    
    </form>
@endsection
    


