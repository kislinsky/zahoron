@extends('account.agency.components.page')
@section('title', "Завершенные заявки по облогораживанию")

@section('content')

    {{ view('account.agency.components.aplication.beautification.show-aplications',compact('aplications')) }}

@endsection