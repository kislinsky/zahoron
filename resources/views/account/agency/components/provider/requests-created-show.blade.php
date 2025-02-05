
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
                <div class="title_middle text_decoration">Запрос от {{$request->created_at->format('d.m.Y')}}</div>
                <div class="text_black margin_top_20"><strong>Название:</strong> {{$request->title}}</div>
                <div class="text_black margin_top_20"><strong>Описание:</strong> {{$request->content}}</div>
                
                <div class="ul_images_offers_to_provider">
                    @foreach(json_decode($request->images) as $image)
                        <div class="li_image_to_provider">
                            <img src="{{asset('storage/uploads_product/'.$image)}}" alt="{{$request->title}}">    
                        </div>            
                    @endforeach
                </div>
            </div>
        </div>
            
        @endforeach
        {{ $requests->withPath(route('account.agency.provider.offer.created'))->appends($_GET)->links() }}

    </div>
@endif