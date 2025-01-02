@extends('account.agency.components.page')
@section('title', "Завершенные заказы с маркетплэйса")

@section('content')
    {{view('account.agency.components.product.orders-complited',compact('orders'))}}       

@endsection