@if(isset($districts))
    @if(count($districts)>0)
        @foreach($districts as $district)
            <option value="{{$district->id}}">{{$district->title}}</option>
        @endforeach
    @endif
@endif