@if(isset($category) && $category!=null)
    <div class="index_title"><span class="cat_title">{{$category->title}}</span> 
    
    @if(isset($district) && $district!=null)    
        <span class='cemetery_title'>{{$district->title}} район</span>
    @endif
    @if(isset($cemetery) && $cemetery!=null)
    
        <a class='cemetery_title'href="{{route('cemeteries.single',$cemetery->id)}}">{{$cemetery->title}} кладбище</a>
    @endif
    в городе {{$city->title}}</div>
@endif