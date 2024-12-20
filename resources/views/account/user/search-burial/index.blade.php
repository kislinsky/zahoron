@extends('account.user.components.page')

@section('title','Поиск могил')

@section('content')



<section class="orders">
    <div class="flex_titles_account">
        <div class="flex_titles_account">
            <a href='{{ route('account.user.burial-request.index') }}?status=0'class="btn_bac_gray<?php if($status!=null && $status==0){echo ' active_label_product';}?>">В работе </a>
            <a href='{{ route('account.user.burial-request.index') }}?status=2'class="btn_bac_gray<?php if($status!=null && $status==2){echo ' active_label_product';}?>">Найдено </a>
            <a href='{{ route('account.user.burial-request.index') }}?status=1'class="btn_bac_gray<?php if($status!=null && $status==1){echo ' active_label_product';}?>">Отказ </a>
        </div>
    </div>
    <div class="grid_two margin_top_20">
        {{view('account.user.components.burial-request.show',compact('search_burials'))}}
    </div>
    {{ $search_burials->withPath(route('account.user.burial-request.index'))->appends($_GET)->links() }}

</section>

@endsection