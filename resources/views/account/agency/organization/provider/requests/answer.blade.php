@extends('account.agency.components.page')
@section('title', "Ответы на заявки по запросу стоимости")

@section('content')

{{view('account.agency.components.provider.offer-answer-show',compact('requests'))}}

@endsection