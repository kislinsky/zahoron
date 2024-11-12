@if($children_categories!=null && count($children_categories)>0)
    @foreach ($children_categories as $children_category)
        <option value="{{$children_category->id}}">{{$children_category->title}}</option>
    @endforeach
@endif