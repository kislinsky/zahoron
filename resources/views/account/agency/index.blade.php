@extends('account.agency.components.page')
<?php $title="Здравствуйте, {$user->name}!  Добро пожаловать в ваш личный кабинет.";?>
@section('title', $title)

@section('content')
<div class="container mt-4">

   {{ view('account.agency.components.message-not-organizations',compact('haveOrganizations')) }}
    <h2 class="mb-4">Уведомления</h2>
    
    @if($notifications && $notifications->count() > 0)
        <div class="list-group">
            @foreach($notifications as $notification)
                <div class="list-group-item list-group-item-action mb-3 rounded shadow-sm">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">{{ $notification->title }}</h5>
                        <div class="text_black">
                            {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                        </div>
                    </div>
                    <p class="text_black">{{ $notification->message }}</p>
                </div>
            @endforeach
        </div>
        
        
    @else
        <div class="alert alert-info" role="alert">
            У вас пока нет уведомлений.
        </div>
    @endif
</div>
@endsection