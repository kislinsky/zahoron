@if($products->count()>0)
    <div class="ul_products_organizations">
        @foreach ($products as $product)
            <div class="li_product_organization">
                <div class="flex_product_organization">
                    <a href='{{$product->route()}}' class="text_middle_index underline" >{{$product->title}}</a>
                    <a href='{{route('account.agency.delete.product',$product->id)}}'class="delete_product_organization"><img src="{{asset('storage/uploads/Vector (16).svg')}}" alt=""></a>
                </div>

                {{view('account.agency.components.product.charastericts-li-product',compact('product'))}}
                

                <div class="li_charasteristic_product">
                    <div class="text_black_bold">Цена</div>
                    <input type="number" class="input_price_update_product_organization" value='{{$product->total_price}}'>
                    <input type="hidden" name="product_id" value='{{$product->id}}'>
                    <div class="btn_update_product_organization">
                        <img src="{{asset('storage/uploads/loading.svg')}}" alt="">
                    </div>
                </div>
        

                <div class="grid_two_btn">
                    <div class="blue_btn">Редактировать</div>
                    <a  href='{{$product->route()}}' class="gray_btn">Посмотреть</a>
                </div>
            </div>
        @endforeach
    </div>
    {{ $products->withPath(route('account.agency.products'))->appends($_GET)->links() }}


    <script>
        
    $( ".btn_update_product_organization" ).on( "click", function() {
        let price = $(this).siblings('.input_price_update_product_organization').val()
        let product_id = $(this).siblings('input[name="product_id"]').val()
        let img=$(this).children('img')
        let block_main=$(this).parent('.li_charasteristic_product')
        img.addClass('rotate_block')
        $.ajax({
            type: 'GET',
            url: '{{ route("account.agency.update.product.price") }}',
            data: {
                "_token": "{{ csrf_token() }}",
                'price': price,
                'product_id': product_id,
            }, success: function (result) {
                window.setTimeout(() => {
                    img.removeClass('rotate_block')
                    $(block_main).append(result)
                    window.setTimeout(() => {
                        $(".green_btn").fadeOut()
                    }, 4000)
                }, 500);
            },
            error: function () {
                window.setTimeout(() => {
                    img.removeClass('rotate_block')
                    $(block_main).append(result)
                }, 1000);
                alert('Ошибка');
            }
        });

    });
    </script>
@else
    <div class="text_black">
        У вас нет привязанной организации
    </div>
@endif