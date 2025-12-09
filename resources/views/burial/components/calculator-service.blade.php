<div class="block_calculator_price_service">
    <div class="title_middle">Выберите размер участка</div>
    <form   method='get' class='form_services_add'>
        @csrf
       @php
    $sizes = get_acf(20, 'ul_sizes_plot');
@endphp

@if($sizes && !empty(trim($sizes)))
    @php
        $sizeArray = array_map('trim', explode('|', $sizes));
        $sizeArray = array_filter($sizeArray);
    @endphp
    
    @if(!empty($sizeArray))
        <select name="size" id="size-select">
            <option value="">Выберите размер</option>
            @foreach($sizeArray as $size)
                <option value="{{ $size }}">{{ $size }}</option>
            @endforeach
        </select>
    @endif
@endif
        @if (isset($services))
            @if ($services->count()>0)
                @foreach ($services as $service)
                    <label class='checkbox'>
                        @if (selectCity()->cemeteries->first()!=null)
                            <input price={{$service->getPriceForCemetery(selectCity()->cemeteries->first()->id)  }} type="checkbox" name="service[]" value='{{ $service->id }}'>
                            <a href='{{ $service->route() }}'class="text_block_mini">{{ $service->title }}</a>
                            <div class="title_middle">{{ $service->getPriceForCemetery(selectCity()->cemeteries->first()->id) }} ₽</div>  
                        @else
                            <input price={{$service->price  }} type="checkbox" name="service[]" value='{{ $service->id }}'>
                            <a href='{{ $service->route() }}'class="text_block_mini">{{ $service->title }}</a>
                            <div class="title_middle">{{ $service->price }} ₽</div>   
                        @endif
                        
                       
                    </label>
                @endforeach
            @endif
        @endif
        
        <div class="flex_form_services_add">
            <div class="block_total">
                <div class="text_block_mini">Итого услуг на сумму:</div>
                <div class="title_middle"><p>0</p> ₽</div>
            </div>
        </div>
    </form>
</div>