@extends('account.agency.components.page')
@section('title', "Модерация отзывов")

@section('content')

    <div class="text_black margin_top_down_20">Здесь вы можете просматривать отзывы об организациях  и товарах, которые были приобретены.</div>
    
    <div class="flex_btn margin_top_down_20">
        <a href='{{route('account.agency.reviews.organization')}}' class="blue_btn">О компании</a>
        <a href='{{route('account.agency.reviews.product')}}' class="gray_btn">О товарах</a>
    </div>

    <div class="text_black_bold text_align_start">Все отзывы</div>

    {{view('account.agency.components.reviews.show-organizations',compact('reviews'))}}

@endsection
