@extends('account.agency.components.page')
@section('title', "Завершенные заявки по умершим")

@section('content')

    {{ view('account.agency.components.aplication.find-out-information-about-deceased.show-aplications',compact('aplications')) }}

@endsection