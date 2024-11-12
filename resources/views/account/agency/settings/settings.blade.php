@extends('account.agency.components.page')
@section('title', 'Настройки пользователя')

@section('content')

    <form action='{{ route('account.organization.settings.update') }}' method='post' enctype='multipart/form-data' class="form_settings">
    @csrf
    
    {{view('account.agency.components.settings-user.main-info',compact('user'))}}

    {{view('account.agency.components.settings-user.data-for-contract',compact('user'))}}
    
    {{view('account.agency.components.settings-user.pay',compact('user'))}}
 
    {{view('account.agency.components.settings-user.notification',compact('user'))}}

    {{view('account.agency.components.settings-user.others',compact('user'))}}
   
    <button type="submit" class='blue_btn settings_margin_form'>Сохранить настройки</button>
    
    </form>
@endsection
    


