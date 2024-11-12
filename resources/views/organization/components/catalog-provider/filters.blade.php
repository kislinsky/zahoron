<div class="flex_filters_organizaitons">
    <div class="filter_block_organization">
        <label  for="">Выберите город</label>

        <select  name="city_id" id="city_id" class='active_select_filter_organiaztion_2'>
            <option disabled value="0">Выберите город</option>
            @if($city_all->count()>0)
                @foreach ($city_all as $city_one)
                <option <?php if(isset($city) && $city!=null && $city->id==$city_one->id){echo 'selected';}?> value="{{$city_one->id}}">{{$city_one->title}}</option>

                @endforeach
            @endif
        </select>
    </div>
    <div class="filter_block_organization block_input">
        <input type="text" name="name_organization " id="name_organization" placeholder="Поиск фирмы">
    </div>
    <label class="filter_block_organization filter_sort">
        <img src="{{asset('/storage/uploads/iconoir_sort.svg')}}" alt=""><span val='{{nameSort($sort)}}' class='name_sort'>{{nameSort($sort)}}</span>
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