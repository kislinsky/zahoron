<div class="ul_charasteristic_product">
    <div class="li_charasteristic_product"><div class="text_black_bold">Категория:</div><div class="text_gray">{{$product->parentCategory()->title}}</div></div>
    <div class="li_charasteristic_product"><div class="text_black_bold">Подкатегория:</div><div class="text_gray">{{$product->category()->title}}</div></div>


    @if($product->category()->type=='beatification')
        <div class="li_charasteristic_product"><div class="text_black_bold">Размер:</div><div class="text_gray">{{$product->size}}</div></div>
        <div class="li_charasteristic_product"><div class="text_black_bold">Материал:</div><div class="text_gray">{{$product->material}}</div></div>
    
    @else
        <div class="li_charasteristic_product"><div class="text_black_bold">Координаты:</div><div class="text_gray">{{$product->location_width}},{{$product->location_longitude}}</div></div>
    @endif

</div>
