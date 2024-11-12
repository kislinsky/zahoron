@if(isset($mortuaries))
    @if(count($mortuaries)>0)
        @foreach($mortuaries as $mortuary)
            <option value="{{$mortuary->id}}">{{$mortuary->title}}</option>
        @endforeach
    @endif
@endif