@foreach ($cities as $city)
    <option value="{{$city->id}}">{{$city->title}}</option>
@endforeach