<div class="ul_organizaiotns margin_top_20">
    
    @foreach($requests as $request)
    <div class="li_request_answer">
        <div class="text_flex">
            <form method='post' action='{{route('account.agency.provider.request.delete',$request->id)}}'class="delete_offer">
                @csrf
                @method('DELETE')
                <button><img src='/storage/uploads/Закрыть_blue.svg' ></button>
            </form>
            <div class="title_middle text_decoration">Заявка от {{$request->created_at->format('d.m.Y')}}</div>
        </div>
       
        <div class="li_organization_provider">
            <div class="li_provider_flex li_bottom_gray_border">
                <div class="flex_gap_5_column">
                    <div class="title_checkout_form text_decoration">Фирма {{$request->organizationProvider()->title}}</div>
                    <div class="text_black"><strong>Город:</strong>{{$request->organizationProvider()->city()->title}}</div>
                    <div class="text_black"><strong>Итоговая цена:</strong>{{$request->price}} ₽</div>
                </div>
                <div class="content_li_provider">
                    <div class="flex_awards">
                        @if($request->organizationProvider()->awards!=null)
                            <?php $awards=json_decode($request->organizationProvider()->awards);?>
                            @if($awards!=null)
                                @foreach($awards as $award)
                                    <img src="{{asset('storage/uploads_organization/'.$award)}}" alt="">
                                @endforeach
                            @endif
                        @endif
                    </div>
                </div>
            </div> 

            <div class="ul_answer_product_request">
                @foreach(json_decode($request->products) as $product)
                    <div class="text_black"><strong>{{$request->findProduct($product[0])->title}}: </strong> <span class='text_gray'></span>{{$product[2]}} ₽</div>
                @endforeach
            </div>

            <div class="li_provider_flex">
                <div class="flex_gap_10_column">
                    <div class="text_black"><strong>Тк:</strong>
                        @foreach(json_decode($request->transport_companies) as $transport_company)
                            {{$transport_company}}, 
                        @endforeach
                    </div>
                    <div class="text_black"><strong>Доставка:</strong>{{$request->price_transport_companies}} ₽</div>
                </div>
                <div class="flex_btn">
                    <a href='{{$request->organizationProvider()->route()}}' class="blue_btn">Страница</a>
                    <div class="gray_btn">Чат</div>
                </div>
            </div>

            <div class="li_provider_flex"></div>
        </div>
    </div>
        
    @endforeach
    {{ $requests->withPath(route('account.agency.provider.offer.answers'))->appends($_GET)->links() }}

</div>