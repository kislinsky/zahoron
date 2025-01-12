
<div class="block_product_cats">
    <div class="title_cat_marketplace">Категории</div>
    <div class="ul_cats_marketplace">
    @if(isset($cats))
        @if($cats->count()>0)
            @foreach ($cats as $cat)
                <div class="main_cat">

                    <div id_category='{{ $cat->id }}' class="li_cat_main_marketplace"><img class='icon_black'src="{{ asset('storage/uploads_cats_product/'.$cat->icon) }}" alt=""> <img class='icon_white'src="{{ asset('storage/uploads_cats_product/'.$cat->icon_white) }}" alt="">{{ $cat->title }}</div>
                    <?php $cats_children=childrenCategoryProducts($cat);?>
                    @if (count($cats_children)>0)
                        <ul class="ul_childern_cats_marketplace">
                            @foreach ($cats_children as $cat_children)
                                <li slug='{{$cat_children->slug}}'  id_category={{ $cat_children->id }} class='li_cat_children_marketplace <?php if($category!=null){if($category->id==$cat_children->id){echo 'active_category';}}?>'>{{ $cat_children->title }}</li>
                            @endforeach
                        </ul>    
                    @endif
                    
                </div>
            @endforeach
        @endif
    @endif
    </div>
</div>