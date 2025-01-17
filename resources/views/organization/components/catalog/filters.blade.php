<div class="flex_filters_organizaitons">
    <div class="filter_block_organization">
        <label id='label_select_1' class='<?php if($district_choose==null || ($district_choose==null && $cemetery_choose==null)){echo 'active_label_select';}?>' for="">Выберите кладбище</label>
        <label id='label_select_2' class='<?php if($cemetery_choose==null && $district_choose!=null){echo 'active_label_select';}?>' for="">Выберите район</label>

        <select  name="cemetery_id" id="cemetery_id" class='<?php if($district_choose==null || ($district_choose==null && $cemetery_choose!=null)){echo 'active_select_filter_organiaztion_2';}?>'>
            <option disabled value="0">Выберите кладбище</option>
            @if($cemeteries->count()>0)
                @foreach ($cemeteries as $cemetery)
                <option <?php if($cemetery_choose!=null && $cemetery_choose->id==$cemetery->id){echo 'selected';}?> value="{{$cemetery->id}}">{{$cemetery->title}}</option>

                @endforeach
            @endif
        </select>
        <select  name="district_id" id="district_id" class='<?php if($cemetery_choose==null && ($district_choose!=null && $district_choose!='null')){echo 'active_select_filter_organiaztion_2';}?>'>
            <option disabled value="0">Выберите район</option>
            @if($districts->count()>0)
                @foreach ($districts as $district)
                <option  value="{{$district->id}}">{{$district->title}}</option>

                @endforeach
            @endif
        </select>
    </div>
    
    <label class="filter_block_organization filter_sort">
        <img class='img_light_theme'src="{{asset('/storage/uploads/iconoir_sort.svg')}}" alt="">
        <img class='img_black_theme'src="{{asset('/storage/uploads/Vector (54)_black.svg')}}" alt="">
        <span val='{{nameSort($sort)}}' class='name_sort'>{{nameSort($sort)}}</span>
        <div class="ul_sort">
            <div val='price_down'class="li_sort">По убыванию цены</div>
            <div val='price_up' class="li_sort">По возрастанию цены</div>
            <div val='date' class="li_sort">По новизне</div>
            <div val='popular' class="li_sort">По популярности</div>
        </div>

    </label>

    <div class="filter_block_organization">
        <label class='checkbox <?php if(isset($filter_work) && $filter_work=='on'){echo 'active_checkbox';}?>'><input required value='1'  <?php if(isset($filter_work) && $filter_work=='on'){echo 'checked';}?> class='filter_work' type="checkbox" name="filter_work" id="filter_work">работает сейчас</label>
    </div>
</div>