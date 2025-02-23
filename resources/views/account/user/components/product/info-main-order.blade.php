@if($product->type=='beautification')
    <div class="mini_flex_li_product">
        <div class="text_black">Размер: {{ $order->size }}</div>
        <div class="text_black">Кладбище: <a href="{{ $order->cemetery->route() }}">{{ $order->cemetery->title }}</a></div>
        <div class="text_black">материал: {{ $product->material }}</div>
    </div>

@elseif($product->type=='memorial-menu') 

    <div class="mini_flex_li_product">
        <div class="text_black">Дата: {{ $order->date }}</div>
        <div class="text_black">Время: {{ $order->time }}</div>
    </div>

@elseif($product->type=='organization-cremation') 

<div class="text_black">Морг: <a href="{{ $order->mortuary->route() }}">{{ $order->mortuary->title }}</a></div>


@elseif($product->type=='shipping-cargo-200') 

    <div class="mini_flex_li_product">
        <div class="text_black">Морг: <a href="{{ $order->mortuary->route() }}">{{ $order->mortuary->title }}</a></div>
        <div class="text_black">Город отправки: {{ $order->city_from }}</div>
        <div class="text_black">Город прибытия: {{ $order->city_to }}</div>
    </div>

@elseif($product->type=='digging-graves') 
    <div class="mini_flex_li_product">
        <div class="text_black">Кладбище: <a href="{{ $order->cemetery->route() }}">{{ $order->cemetery->title }}</a></div>
    </div>

@elseif($product->type=='funeral-arrangements') 
    <div class="mini_flex_li_product">
        <div class="text_black">Кладбище: <a href="{{ $order->cemetery->route() }}">{{ $order->cemetery->title }}</a></div>
        <div class="text_black">Морг: <a href="{{ $order->mortuary->route() }}">{{ $order->mortuary->title }}</a></div>
    </div>

@endif