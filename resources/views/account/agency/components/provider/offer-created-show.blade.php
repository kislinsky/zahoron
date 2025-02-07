
@if($requests->count()>0) 

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
        
            <div class="li_organization_provider created_offers">
                <div class="ul_answer_product_request">
                    @foreach(json_decode($request->products) as $product)
                        <div class="text_black"><strong>{{$request->findCategory($product[0])->title}}: </strong> {{$product[1]}} шт.</div>
                    @endforeach
                </div>

                <div class="li_provider_flex">
                    <div class="flex_gap_10_column">
                        <div class="text_black"><strong>Тк:</strong>
                            @if(json_decode($request->transport_companies)=='all' )
                                Любая
                            @else
                                @foreach(json_decode($request->transport_companies) as $transport_company)
                                    {{$transport_company}}, 
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
            
        @endforeach
        {{ $requests->withPath(route('account.agency.provider.offer.created'))->appends($_GET)->links() }}

    </div>
@endif