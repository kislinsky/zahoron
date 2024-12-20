@extends('account.user.components.page')

@section('title','Геолокации')

@section('content')

    <section class="orders">
        <div class="flex_titles_account">
            <a href='{{ route('account.user.burial') }}?status=1'class="btn_bac_gray">Оплаченные</a>
            <a href='{{ route('account.user.burial') }}?status=0'class="btn_bac_gray">Ожидают оплаты </a>
            <a href='{{ route('account.user.burial.favorite') }}'class="blue_btn favorite_btn"><img src='{{ asset('storage/uploads/Star_white.svg') }}'> Избранное </a>

        </div>
        <div class="grid_two margin_top_20">
            {{view('account.user.components.burials.show-favorite',compact('favorite_burials'))}}
        </div>
        {{ $favorite_burials->withPath(route('account.user.burial.favorite'))->appends($_GET)->links() }}

    </section>

@endsection




