@extends('account.agency.components.page')
@section('title', "Созданные заявки по запросу стоимости")

@section('content')

{{view('account.agency.components.provider.offer-created-show',compact('requests'))}}

@endsection