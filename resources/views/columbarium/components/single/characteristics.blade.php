<div class="block_content_organization_single info_about_organization">
    <div class="title_li title_li_organization_single">{{$columbarium->title}} колумбарий</div>
    @if($characteristics!=null && count($characteristics)>0)
        @foreach ($characteristics as $characteristic)
            <div class="text_black">{{$characteristic[0]}}: {{$characteristic[1]}}</div>        
        @endforeach
    @endif
    
   
</div>