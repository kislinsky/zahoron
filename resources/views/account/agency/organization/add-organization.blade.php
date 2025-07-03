@extends('account.agency.components.page')
@section('title', 'Добавить организацию')

@section('content')

    {{view('account.agency.components.search-organization.form-search',compact('s','city'))}}

    {{view('account.agency.components.search-organization.show',compact('organizations','city'))}}

@endsection
