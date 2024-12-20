@extends('account.user.components.page')
<?php $title="Здравствуйте, {$user->name}!  Добро пожаловать в ваш личный кабинет.";?>
@section('title', $title)

@section('content')


<section class="orders">
    <div class="title_middle">Последние заказы</div>
    <div class="grid_two margin_top_20">

        {{view('account.user.components.burials.show',compact('orders_burials'))}}

        {{view('account.user.components.services.show',compact('orders_services'))}}
        
    </div>
</section>
@endsection