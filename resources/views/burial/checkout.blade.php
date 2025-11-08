@include('header.header')

<section class="order_page bac_gray">
    <div class="container order_page_search">
        <div class="content_order_page">
            <h1 class="index_title">Оформление заказа</h1>    
        </div>
        <img class='img_light_theme rose_checkout'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        <img class='img_black_theme rose_checkout'src="{{asset('storage/uploads/rose-with-stem 1_black.svg')}}" alt="">
    </div>
</section>

<section class="checkout">
    <div class="container">
        <div class="title_middle">Ваш заказ</div>
        @if (isset($cart_items))
            @if (count($cart_items) > 0)
                <table class='checkout_table'>
                    <thead>
                        <th class='title_burial_basket'>Услуга</th>
                        <th>Захоронение</th>
                        <th>Статус геолокации</th>
                        <th>Сумма</th>
                        <th></th>
                    </thead>
                    <?php $total = 0; ?>
                    @foreach ($cart_items as $cart_item)
                        <?php 
                            $product = getBurial($cart_item);
                            $is_purchased = OrderBurial::where('burial_id', $cart_item)
                                ->where('user_id', Auth::id())
                                ->where('status', 1)
                                ->exists();
                            
                            $item_total = $product->cemetery->price_burial_location;
                            $total += $item_total;
                        ?>
                        <tr>
                            <td class='title_cart title_burial_basket'>Предоставление геолокации</td>
                            <td><a href='{{ $product->route() }}' class="title_cart decoration_on">{{ $product->surname }} {{ $product->name }} {{ $product->patronymic }}</a></td>
                            <td class='title_cart'>
                                @if($is_purchased)
                                    <span style="color: green;">✓ Уже куплена</span>
                                @else
                                    <span style="color: orange;">Требуется покупка</span>
                                @endif
                            </td>
                            <td class='title_cart'>{{ $item_total }} ₽</td>
                            <td><a href='{{ route('burial.delete',$product->id) }}' class="delete_cart"><img src="{{asset('storage/uploads/Trash.svg')}}" alt=""> Удалить</a></td>
                        </tr>
                    @endforeach
                    
                    <tfoot>
                        <tr>
                            <td colspan="3"><div class="title_cart">Итого:</div></td>
                            <td><div class="title_middle">{{ $total }} ₽</div></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            @endif
        @else
            <div class="title_middle">{{ $no_items }}</div>
        @endif
        
        {{view('burial.components.checkout.form-checkout',compact('user'))}}
    </div>
</section>

@include('footer.footer')