@if(isset($cemeteries_beatification))
    @if(count($cemeteries_beatification)>0)
        @foreach($cemeteries_beatification as $cemetery_beatification)
            <option value="{{$cemetery_beatification->id}}">{{$cemetery_beatification->title}}-{{$cemetery_beatification->adres}}</option>
        @endforeach
    @endif
@endif