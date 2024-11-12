@include('header.header')

<section class="order_page bac_gray">
    <div class="container order_page_search">
        <div class="content_order_page">
            <div class="index_title">Оформление заказа</div>    
        </div>
        <img class='rose_checkout'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        
    </div>
</section>

<section class="checkout">
    <div class="container">
        <div class="title_middle">Ваш заказ</div>
        @if (isset($cart_items))
            @if (count($cart_items)>0)
                <table class='checkout_table'>
                    <thead>
                        <th>Услуга</th>
                        <th>Захоронение</th>
                        <th>Размер участка</th>
                        <th>Сумма</th>
                        <th></th>
                    </thead>
                    <?php $total=0; ?>
                    @foreach ($cart_items as $cart_item)
                        <?php $product=getBurial($cart_item[0]);
                            $services=servicesBurial($cart_item[1]);?>
                                @foreach ($services as $service)
                                <?php $total+=$service->price;?>
                                    <tr>
                                        <td class='title_cart'><a href="{{ route('service.single',$service->id) }}">{{ $service->title }}</a></td>
                                        <td><a href='{{ $product->route() }}' class="title_cart decoration_on">{{ $product->surname }} {{ $product->name }} {{ $product->patronymic }}</div></td>
                                        <td class='title_cart'>{{ $service->price }} ₽</td>

                                        <td>
                                            <form method='get'action='{{ route('burial.service.delete') }}'>
                                                @csrf 
                                                <input type="hidden" name="product_id"  value='{{ $product->id }}'>
                                                <input type="hidden" name='service_id' value='{{ $service->id }}'>
                                                <button type='submit' class="delete_cart"><img src="{{asset('storage/uploads/Trash.svg')}}" alt=""> Удалить</button></form>
                                        </td>
                                    </tr>
                                @endforeach
                                
                    @endforeach
                    
                    <tfoot>
                        <tr>
                            <td><div class="title_cart">Итого:</div></td>
                            <td><div class="title_middle">{{ $total }} ₽</div></td>
                        </tr>
                    </tfoot>
                </table>
            @endif
        @else
                <div class="title_middle">{{ $no_items }}</div>
        @endif

        {{view('service.components.form-checkout',compact('user'))}}
    </div>
</section>

@include('footer.footer') 