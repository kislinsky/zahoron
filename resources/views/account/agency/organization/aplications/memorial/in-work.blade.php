@extends('account.agency.components.page')
@section('title', "Заявки в работе по поминкам")

@section('content')

    {{ view('account.agency.components.aplication.memorial.show-aplications',compact('aplications')) }}

@endsection