
<div class="title">Стоимость {{$category_main->title}} : {{$category->title}} 
    @if($district_choose!=null)
        в {{$district_choose->title}} районе
    @endif
    @if($cemetery_choose!=null)
        в {{$cemetery_choose->title}} кладбище
    @endif
    
г. {{$city->title}}</div>
