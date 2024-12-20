@extends('account.user.components.page')

@section('title','Услуги')

@section('content')



<section class="orders">
    <div class="flex_titles_account">

        <div class="flex_titles_account">
            <a href='{{ route('account.user.services.index') }}?status=1'class="btn_bac_gray <?php if($status!=null && $status==1){echo ' active_label_product';}?>">Оплаченные </a>
            <a href='{{ route('account.user.services.index') }}?status=0'class="btn_bac_gray <?php if($status!=null && $status==0){echo ' active_label_product';}?>">Ожидают оплаты </a>
            <a href='{{ route('account.user.services.index') }}?status=2'class="btn_bac_gray <?php if($status!=null && $status==2){echo ' active_label_product';}?>">В работе </a>
            <a href='{{ route('account.user.services.index') }}?status=3'class="btn_bac_gray <?php if($status!=null && $status==3){echo ' active_label_product';}?>">Исполненные </a>
        </div>

    </div>
    <div class="grid_two margin_top_20">
        {{view('account.user.components.services.show',compact('orders_services'))}}
    </div>
    {{ $orders_services->withPath(route('account.user.services.index'))->appends($_GET)->links() }}

</section>

@endsection