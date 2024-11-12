@if($sales!=null && $sales->count()>0)
    @php $sales=json_decode($sales->first()->sales);@endphp
    @foreach($sales as $sale)
        <div class="sale_text">
            <div class="blue_title">-{{$sale[0]}}%</div> <span class="gray_mini_text"> — {{$sale[1]}}</span>
        </div>
    @endforeach
@endif