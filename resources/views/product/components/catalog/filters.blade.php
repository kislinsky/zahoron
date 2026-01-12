<div id='filter_1' class="flex_filters_products marketplace_filters <?php if($category->parent_id==36){echo 'active_filters';}?>">
    <label class="filter_block_organization filter_sort">
        <img class='img_light_theme'src="{{asset('/storage/uploads/iconoir_sort.svg')}}" alt="">
        <img class='img_black_theme'src="{{asset('/storage/uploads/Vector (54)_black.svg')}}" alt="">
        <span val='{{@nameSort($sort)}}' class='name_sort'>{{nameSort($sort)}}</span>
        <div class="ul_sort">
            <div val='price_down'class="li_sort">По убыванию цены</div>
            <div val='price_up' class="li_sort">По возрастанию цены</div>
            <div val='date' class="li_sort">По новизне</div>
            {{-- <div val='popular' class="li_sort">По популярности</div> --}}
        </div>
    </label>

    

    <div class="filter_block">
        <select name="cemetery_id" id="cemetery_id">
            @if($cemeteries_all!=null)
                @foreach($cemeteries_all as $cemeteries_one)
                    <option  <?php if(isset($cemetery) && $cemetery->id==$cemeteries_one->id){echo 'selected';}?> value="{{ $cemeteries_one->id}}">{{ $cemeteries_one->title}}</option>
                   
                @endforeach
            @endif
          
        </select>
    </div>
    
    <div class="filter_block">
        <select name="size" id="size">
            <option  value="Размер">Размер</option>
            <?php $sizes=sizesProducts();?>
            @if($sizes!=null && count($sizes)>0)
                @foreach($sizes as $size)
                    @if($size!=null && $size!='')
                        <option value="{{$size}}">{{$size}}</option>
                    @endif
                @endforeach
            @endif
        </select>
    </div>

    <div class="filter_block">
        <select name="layering" id="layering">
            @if($layerings!=null && count($layerings)>0)
                @foreach($layerings as $layering)
                    @if($layering!=null && $layering!='')
                        <option <?php if(isset($_GET['layering'])){if($_GET['layering']==$layering){echo 'selected';}}?> value="{{$layering}}">{{$layering}}</option>
                    @endif
                @endforeach
            @endif
        </select>
    </div>

    

    <div class="filter_block">
        <select name="material" id="material">
            <option  value="Материал">Материал</option>
            @foreach ($materials_filter as $material_filter)
                @if($material_filter!=null && $material_filter!='')
                <option <?php if(isset($_GET['material'])){if($_GET['material']==$material_filter){echo 'selected';}}?>  value="{{ $material_filter }}">{{ $material_filter }}</option>
                @endif
            @endforeach

        </select>
    </div>
</div>

<div id='filter_2' class="flex_filters_products marketplace_filters <?php if($category->parent_id==31){echo 'active_filters';}?>">
    <label class="filter_block_organization filter_sort">
        <img src="{{asset('/storage/uploads/iconoir_sort.svg')}}" alt=""><span val='{{@nameSort($sort)}}' class='name_sort'>{{@nameSort($sort)}}</span>
        <div class="ul_sort">
            <div val='price_down'class="li_sort">По убыванию цены</div>
            <div val='price_up' class="li_sort">По возрастанию цены</div>
            <div val='date' class="li_sort">По новизне</div>
            {{-- <div val='popular' class="li_sort">По популярности</div> --}}
        </div>
    </label>
</div>

<div id='filter_3' class="flex_filters_products marketplace_filters <?php if($category->parent_id==45){echo 'active_filters';}?>">
    <label class="filter_block_organization filter_sort">
        <img src="{{asset('/storage/uploads/iconoir_sort.svg')}}" alt=""><span val='{{@nameSort($sort)}}' class='name_sort'>{{@nameSort($sort)}}</span>
        <div class="ul_sort">
            <div val='price_down'class="li_sort">По убыванию цены</div>
            <div val='price_up' class="li_sort">По возрастанию цены</div>
            <div val='date' class="li_sort">По новизне</div>
            {{-- <div val='popular' class="li_sort">По популярности</div> --}}
        </div>
    </label>

    <div class="filter_block">
        <select name="district_id" id="district_id">
            @if($districts_all!=null)
                @foreach($districts_all as $district_one)
                    <option  <?php if(isset($cemetery) && $cemetery->id==$cemeteries_one->id){echo 'selected';}?>  value="{{ $district_one->id}}">{{ $district_one->title}}</option>
                @endforeach
            @endif
          
        </select>
    </div>

    
</div>