@extends('account.agency.components.page')
@section('title', "Акции, скидки поставщиков")

@section('content')

    <div class="text_black ">{{get_acf(10,'content')}}</div>

    <div class="flex_btn margin_top_down_20">
        <a href='{{route('account.agency.provider.stocks')}}' class="blue_btn">Акции</a>
        <a href='{{route('account.agency.provider.discounts')}}' class="gray_btn">Скидки</a>
    </div>

    {{view('account.agency.components.provider.stocks-show',compact('stocks'))}}

@endsection