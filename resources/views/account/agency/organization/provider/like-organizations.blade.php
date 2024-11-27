@extends('account.agency.components.page')
@section('title', "Избранные поставщики")

@section('content')
    
    <div class="text_black margin_top_20">{{get_acf(9,'content')}}</div>

    {{view('account.agency.components.provider.organizations-show',compact('organizations'))}}

@endsection