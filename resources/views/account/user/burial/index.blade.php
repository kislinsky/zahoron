@extends('account.user.components.page')

@section('title','Геолокации')

@section('content')



<section class="orders">
    <div class="flex_titles_account">

        <a href='{{ route('account.user.burial') }}?status=1'class="btn_bac_gray<?php if($status!=null && $status==1){echo ' active_label_product';}?>">Оплаченные</a>
        <a href='{{ route('account.user.burial') }}?status=0'class="btn_bac_gray<?php if($status!=null && $status==0){echo ' active_label_product';}?>">Ожидают оплаты </a>
        <a href='{{ route('account.user.burial.favorite') }}'class="btn_bac_gray favorite_btn"><img  class='black_icon' src='{{ asset('storage/uploads/Star 1 (2).svg') }}'><img class='white_icon' src='{{ asset('storage/uploads/Star_white.svg') }}'>Избранное </a> 
        
    </div>
    <div class="grid_two margin_top_20">
        {{view('account.user.components.burials.show',compact('orders_burials'))}}
    </div>
    {{ $orders_burials->withPath(route('account.user.burial'))->appends($_GET)->links() }}

</section>

@endsection