@extends('account.user.components.page')

@section('title','Заказы с маркетплейса')

@section('content')
    <section class="orders">

        <div class="flex_titles_account">
            <a href='{{ route('account.user.products') }}?status=0'class="btn_bac_gray <?php if($status!=null && $status==0){echo ' active_label_product';}?>">Новые</a>
            <a href='{{ route('account.user.products') }}?status=1'class="btn_bac_gray <?php if($status!=null && $status==1){echo ' active_label_product';}?>">Принятые</a>
            <a href='{{ route('account.user.products') }}?status=2'class="btn_bac_gray <?php if($status!=null && $status==2){echo ' active_label_product';}?>">Завершенные</a>
        </div>


        

        @if(isset($orders_products))
            @if(count($orders_products)>0)
                {{view('account.user.components.product.show',compact('orders_products'))}}
            @endif
        @endif
        

    </section>



@endsection

