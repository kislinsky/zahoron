<?php 

$cats=mainCategoryProduct();
?>

@if(count($cats)>0)

    <section class='cats_product'>
        <div class="container">
            <h2 class="title">Удобный выбор категорий</h2>
            <div class="ul_cats_product">
                    @foreach ($cats as $cat)
                        @if($cat->display==1)
                        <div class="li_cat_product">
                            <div class="title_news">{{ $cat->title }}</div>
                            <?php $cats_children=childrenCategoryProducts($cat);?>
                            @if (count($cats_children)>0)
                                <div class="ul_children_cat_product">
                                    @foreach ($cats_children as $cat_children)
                                        @if($cat_children->display==1)
                                            <a href="{{ route('organizations.category',$cat_children->slug) }}" class="title_label li_cat_children_product">{{ $cat_children->title }}</a>
                                        @endif
                                    @endforeach
                                </div>    
                            @endif
                            @if (count($cats_children)>2)
                                <div class="white_btn_blue_text more_children_cats_product">Показать еще</div>
                            @endif
                        </div>
                        @endif
                    @endforeach
            
                
            </div>
           
 @if(!versionProject())
                    <a href='{{ route('marketplace') }}' class="blue_btn">Смотреть все товары</a>
                @endif        </div>
    </section>
@endif
