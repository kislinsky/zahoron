@extends('account.agency.components.page')
@section('title', "Завершенные заявки по поминкам")

@section('content')

    {{ view('account.agency.components.aplication.memorial.show-aplications',compact('aplications')) }}

@endsection