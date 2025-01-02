<?php 

use App\Models\Product;
use App\Models\AdditionProduct;
?>
@if (isset($cart_items))
    @if (count($cart_items)>0)
        <table class='checkout_table'>
            <thead>
                <th>Товар</th>
                <th class='table_busket_th_count'>Количество</th>
                <th>Сумма</th>
                <th class='table_busket_th_delete'></th>
            </thead>
            <?php $total=0; ?>
            @foreach ($cart_items as $cart_item)
                <?php $product=Product::findOrFail($cart_item[0]);
                    $total_additionals=0;?>
                    <tr >
                        <td class='title_cart'>
                            <div class="grid_two_cart">
                                <div class='image_product_cart'>
                                    <?php $images=$product->getImages;?>
                                    @if (isset($images))
                                        @if (count($images)>0)
                                            <img  src="{{ asset('storage/uploads_product/'.$images[0]->title) }}" alt="">
                                        @endif
                                    @endif
                                </div>
                                <div class="content_product_cart">
                                    <a href='{{ $product->route() }}'class="title_product_market">{{ $product->title }}</a>
                                    @if($cart_item[3]!='')
                                        <div class="text_li color_black">Размер {{ $cart_item[3] }} </div>
                                    @endif
                                    @if(isset($cart_item[4]))
                                        @if($cart_item[4]!='')
                                            <div class="text_li color_black">Дата: {{ $cart_item[4] }} </div>
                                        @endif
                                    @endif
                                   
                                    @if(isset($cart_item[5]))
                                        @if($cart_item[5]!='')
                                            <div class="text_li color_black">Время: {{ $cart_item[5] }} </div>
                                        @endif
                                    @endif
                                    @if($product->material!=null)
                                    <div class="text_li color_black">{{ $product->material }} </div>
                                    @endif

                                    @if (count($cart_item[1])>0)
                                    <?php $additionals=AdditionProduct::whereIn('id',$cart_item[1])->get();?>
                                        <div class="title_product_market">Дополнения</div>
                                        @foreach ($additionals as $additional)
                                        <?php $total_additionals+=$additional->price;?>
                                            <div class="text_li color_black"> {{$additional->title }} </div>
                                        @endforeach  
                                        
                                    @endif

                                </div>
                            </div>
                        </td>
                        <td class='title_cart count_product_checkout'><input id_product='{{ $product->id }}' type="number" name="count_product" value='{{ $cart_item[2] }}'></td>
                        <td class='title_cart price_cart_checkout'>
                            <div class="count_product_checkout"><input class='change_count_busket' id_product='{{ $product->id }}' type="number" name="count_product" value='{{ $cart_item[2] }}'></div>
                            <div><span>{{ (priceProduct($product)+$total_additionals)*$cart_item[2] }}</span> ₽</div>
                            <a href='{{ route('product.delete',$product->id) }}'class="delete_cart mobile_delete_cart"><img src="{{asset('storage/uploads/Trash.svg')}}" alt=""> Удалить</a>
                        </td>
                        <td><a href='{{ route('product.delete',$product->id) }}'class="delete_cart"><img src="{{asset('storage/uploads/Trash.svg')}}" alt=""> Удалить</a></td>
                    </tr>
                    <?php $total+=(priceProduct($product)+$total_additionals)*$cart_item[2];?>
            @endforeach
            <tfoot>
                
                <tr>
                    <td><div class="title_cart">Итого:</div></td>
                    <td><div class="title_middle total_price_cart"><span>{{ $total }}</span> ₽</div></td>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="title_middle">Добавьте товары в корзину</div>

    @endif
    
@endif


<script>
    $( ".count_product_checkout input" ).on( "change", function() {
        let count= $(this).val();
        let price_block=$(this).parent().siblings('.price_cart_checkout').children('span')
        let id_product= $(this).attr('id_product');
        let block_cart=$(this).parent().parent()
        let old_price=$(this).parent().siblings('.price_cart_checkout').children('span').html()
        $.ajax({
            type: 'GET',
            url: '{{ route("product.cart.count.change") }}',
            data: {
                "_token": "{{ csrf_token() }}",
                'id_product': id_product,
                'count':count,
            }, success: function (result) {
                $('.html_basket').html(result)
            },
            error: function () {
                alert('Ошибка');
            }
        });
    } );
    
</script>