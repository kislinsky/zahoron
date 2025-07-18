<div class="flex_filters_organizaitons_mobile">
    <div class="grid_two">
        <!-- Фильтр категорий -->
        <div class="block_filter_mobile">
            <div class="text_gray_mini">Категория</div>
            <div class="info_block">
                <div class="text_black_bold category-title">
                    
                    @if($category && $category->parent)
                        {{ $category->parent->title }}
                    @elseif($cats->first()!=null)
                        {{ $cats->first()->title }}
                    @endif
                </div>
                <img src="{{ asset('storage/uploads/arrow_down_black.svg') }}" alt="" class="open_mobile_filter_select">
            </div>
            
            <div class="mobile_filter_select category-select">
                @if(isset($cats))
                    @foreach ($cats as $cat)
                        @if($cat->display==1)
                            <div class="mobile_filter_option" data-id="{{ $cat->id }}">{{ $cat->title }}</div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
        
        <!-- Фильтр подкатегорий -->
        <div class="block_filter_mobile">
            <div class="text_gray_mini">Подкатегория</div>
            <div class="info_block">
                <div class="text_black_bold subcategory-title">
                    @if($category)
                        {{ $category->title }}
                    @elseif($cats->first()!=null)
                        <?php $cats_children=childrenCategoryProducts($cats->first());?>
                        @if($cats_children->first())
                            {{ $cats_children->first()->title }}
                        @endif
                    @endif
                </div>
                <img src="{{ asset('storage/uploads/arrow_down_black.svg') }}" alt="" class="open_mobile_filter_select">
            </div>
            
            <div class="mobile_filter_select subcategory-select">
                @if($category && $category->parent)
                    <?php $cats_children=childrenCategoryProducts($category->parent);?>
                @elseif($cats->first()!=null)
                    <?php $cats_children=childrenCategoryProducts($cats->first());?>
                @endif
                
                @if(isset($cats_children) && count($cats_children)>0)
                    @foreach ($cats_children as $cat_children)
                        @if($cat_children->display==1)
                            <div class="mobile_filter_option" data-id="{{ $cat_children->id }}" data-slug="{{ $cat_children->slug }}">{{ $cat_children->title }}</div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    
    <!-- Фильтр кладбищ (показывается для ритуальных услуг и благоустройства) -->
    <div class="block_filter_mobile cemetery-filter">
        <div class="text_gray_mini">Выберите кладбище</div>
        <div class="info_block">
            <div class="text_black_bold cemetery-title">
                @if($cemetery_choose)
                    {{ $cemetery_choose->title }}
                @elseif($cemeteries->count()>0)
                    {{ $cemeteries[0]->title }}
                @endif
            </div>
            <img src="{{ asset('storage/uploads/arrow_down_black.svg') }}" alt="" class="open_mobile_filter_select">
        </div>
        
        <div class="mobile_filter_select cemetery-select">
            <div class="mobile_filter_option" data-id="0">Все кладбища</div>
            @if($cemeteries->count()>0)
                @foreach ($cemeteries as $cemetery)
                    <div class="mobile_filter_option" data-id="{{ $cemetery->id }}">{{ $cemetery->title }}</div>
                @endforeach
            @endif
        </div>
    </div>
    
    <!-- Фильтр районов (показывается для организации поминок) -->
    <div class="block_filter_mobile district-filter" style="display: none;">
        <div class="text_gray_mini">Выберите район</div>
        <div class="info_block">
            <div class="text_black_bold district-title">
                @if($district_choose)
                    {{ $district_choose->title }}
                @elseif($districts->count()>0)
                    {{ $districts[0]->title }}
                @else
                    Все районы
                @endif
            </div>
            <img src="{{ asset('storage/uploads/arrow_down_black.svg') }}" alt="" class="open_mobile_filter_select">
        </div>
        
        <div class="mobile_filter_select district-select">
            <div class="mobile_filter_option" data-id="0">Все районы</div>
            @if($districts->count()>0)
                @foreach ($districts as $district)
                    <div class="mobile_filter_option" data-id="{{ $district->id }}">{{ $district->title }}</div>
                @endforeach
            @endif
        </div>
    </div>
    
</div>


<div class="flex_filters_organizaitons">
    <div class="filter_block_organization filter_place">
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

    <div class="filter_block_organization filter_work">
        <label class='checkbox <?php if(isset($filter_work) && $filter_work=='on'){echo 'active_checkbox';}?>'><input required value='1'  <?php if(isset($filter_work) && $filter_work=='on'){echo 'checked';}?> class='filter_work' type="checkbox" name="filter_work" id="filter_work">работает сейчас</label>
    </div>
</div>


