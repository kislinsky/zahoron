
@if($requests->count()>0) 

    <div class="ul_organizaiotns margin_top_20">
        
        @foreach($requests as $request)
        <div class="li_request_created_to_provider">
            <form method='post' action='{{route('account.agency.provider.offer.delete',$request->id)}}'class="delete_offer">
                @csrf
                @method('DELETE')
                <button><img src='/storage/uploads/Закрыть_blue.svg' ></button>
            </form>
            <div class="li_organization_provider">
                <div class="block_answer_from_provider">
                    <div class="item_answer_from_provider">
                        <div class="title_middle text_decoration">Запрос от {{$request->created_at->format('d.m.Y')}}</div>
                        <div class="text_black"><strong>Название:</strong><br> {{$request->title}}</div>
                    </div>
                    <div class="item_answer_from_provider">
                        <div class="flex_answer_from_provider">              
                            <div class="ul_btn_answer_from_provider">
                                <div class="green_btn text_center">Местное время: 23:40</div>
                                {!!btnOPenOrNot($request->organizationProvider()->openOrNot())!!}
                                <div class="text_black"><strong>Фирма:</strong>{{$request->organizationProvider()->title}}</div>
                                <div class="text_black"><strong>Город:</strong>{{$request->organizationProvider()->city->title}}</div>
                            </div>

                            <div class="ul_btn_answer_from_provider ul_btn_answer_from_provider_mobile">
                                <a href='{{asset('storage/uploads_organization/'.$request->organizationProvider()->price_list)}}' download class="gray_btn">Прайс</a>
                                <a href='{{$request->organizationProvider()->route()}}' class="gray_btn">Страница</a>
                                <div class="gray_btn">Чат</div>
                            </div>
                        </div>
                    </div>
                </div>
                    <div class="ul_images_offers_to_provider">
                        @foreach(json_decode($request->images) as $image)
                            <div class="li_image_to_provider">
                                <img src="{{asset('storage/uploads_product/'.$image)}}" alt="{{$request->title}}">    
                            </div>            
                        @endforeach
                    </div>

                    <div class="text_black"><strong>Ответ: </strong>{{$request->answer}}</div>

                    <table class='table_answer_from_provider'>
                        <tr>
                            <th><span class='text_black_bold'>Доставка:<span></th>
                            <th><span class='text_black_bold'>Срок:<span></th>
                            <th><span class='text_black_bold'>Стоимость доставки:<span></th>
                            <th><span class='text_black_bold'>Стоимость товара:<span></th>
                        </tr>
                        <tr>
                            <td><span class='text_black'>{{$request->name_delivery}}</span></td>
                            <td><span class='text_black'>{{$request->term}}</span></td>
                            <td><span class='text_black'>{{$request->price_delivery}} ₽</span></td>
                            <td><span class='text_black'>{{$request->price_product}} ₽</span></td>
                        </tr>
                    </table>

                    <div class="mobile_block_answer_from_provider">
                        <div class="gray_block_answer text_black_bold">Доставка:</div>
                        <td><span class='text_black'>{{$request->name_delivery}}</span></td>
                        <div class="gray_block_answer text_black_bold">Срок:</div>
                        <td><span class='text_black'>{{$request->term}}</span></td>
                        <div class="gray_block_answer text_black_bold">Стоимость доставки:</div>
                        <td><span class='text_black'>{{$request->price_delivery}} ₽</span></td>
                        <div class="gray_block_answer text_black_bold">Стоимость товара:</div>
                        <td><span class='text_black'>{{$request->price_product}} ₽</span></td>

                    </div>
                </div> 
            </div>
        </div>
            
        @endforeach
        {{ $requests->withPath(route('account.agency.provider.offer.answers'))->appends($_GET)->links() }}

    </div>
@endif