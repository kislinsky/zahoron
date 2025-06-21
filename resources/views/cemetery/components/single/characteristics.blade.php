
@if($characteristics!=null)
    <div class="block_content_organization_single info_about_organization">
        <h2 class="title_li title_li_organization_single">{{$object->title}} </h2>
        @if($characteristics!=null && count($characteristics)>0)
            @foreach ($characteristics as $characteristic)
                <div class="text_black">{{$characteristic[0]}}: {{$characteristic[1]}}</div>        
            @endforeach
        @endif
        
    
    </div>
    @endif