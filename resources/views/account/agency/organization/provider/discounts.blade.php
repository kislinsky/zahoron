@extends('account.agency.components.page')
@section('title', "Акции, скидки поставщиков")

@section('content')

    <div class="text_black ">{{get_acf(11,'content')}}</div>

    <div class="flex_btn margin_top_down_20">
        <a href='{{route('account.agency.provider.stocks')}}' class="gray_btn">Акции</a>
        <a href='{{route('account.agency.provider.discounts')}}' class="blue_btn">Скидки</a>
    </div>

    {{view('account.agency.components.provider.discounts-show',compact('discounts'))}}

@endsection