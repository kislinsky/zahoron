<?php 
use App\Models\District;
use App\Models\CategoryProduct;
use App\Models\ActivityCategoryOrganization;

?>
<div class="block_content_organization_single">
    <div class="ul_memorial_menu">
        @if($organization->name_type!=null)
            <div class="li_memorial_menu">
                <div class="item_memorial_menu item_memorial_menu_1 text_gray">Тип заведения</div>
                <div class="line_gray_menu"></div>
                <div class="item_memorial_menu text_black"> {{$organization->name_type}}</div>
            </div>
         @endif
        @if($organization->next_to!=null)
            <div class="li_memorial_menu">
                <div class="item_memorial_menu item_memorial_menu_1 text_gray">Рядом с</div>
                <div class="line_gray_menu"></div>
                <div class="item_memorial_menu text_black"> {{$organization->next_to}}</div>
            </div>
        @endif
        @if($organization->district_id!=null)
            <div class="li_memorial_menu">
                <div class="item_memorial_menu item_memorial_menu_1 text_gray">Район</div>
                <div class="line_gray_menu"></div>
                <div class="item_memorial_menu text_black"> {{District::find($organization->district_id)->title}}</div>
            </div>
        @endif
        @if($organization->underground!=null)
            <div class="li_memorial_menu">
                <div class="item_memorial_menu item_memorial_menu_1 text_gray">Метро</div>
                <div class="line_gray_menu"></div>
                <div class="item_memorial_menu text_black"> {{$organization->underground}}</div>
            </div>
        @endif
        @if($categories_organization!=null && count($categories_organization)>0)
            @foreach ($categories_organization as $category_organization)
                <div class="li_memorial_menu">
                    <div class="item_memorial_menu item_memorial_menu_1 text_gray">{{$category_organization->title}}</div>
                    <div class="line_gray_menu"></div>
                    <div class="item_memorial_menu text_black"> 
                        @php $categories_children=CategoryProduct::whereIn('id',ActivityCategoryOrganization::where('organization_id',$organization->id)->where('category_main_id',$category_organization->id)->pluck('category_children_id'))->get();@endphp
                        @foreach($categories_children as $category_children)
                           {{$category_children->title}},
                        @endforeach
                    </div>
                </div>
                
            @endforeach
        @endif
        
    </div>
</div>
