@extends('account.agency.components.page')
@section('title', "Заказы в работе с маркетплэйса")

@section('content')
    {{view('account.agency.components.product.orders-in-work',compact('orders'))}}       

@endsection