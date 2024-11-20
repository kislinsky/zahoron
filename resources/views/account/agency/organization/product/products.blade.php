@extends('account.agency.components.page')
@section('title', 'Товары и услуги Маркетплейса')

@section('content')

    {{ view('account.agency.components.product.filters-product',compact('categories','categories_children','city'))}}

    <div class="show_organizations_products_block">
        {{view('account.agency.components.product.show',compact('products'))}}       
    </div>

@endsection