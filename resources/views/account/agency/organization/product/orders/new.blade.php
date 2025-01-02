@extends('account.agency.components.page')
@section('title', "Новые заказы с маркетплэйса")

@section('content')
    {{view('account.agency.components.product.orders-new',compact('orders'))}}       

@endsection