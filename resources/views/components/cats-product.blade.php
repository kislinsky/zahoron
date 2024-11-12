<?php 

use App\Models\CategoryProduct;
$cats=CategoryProduct::orderBy('id','desc')->where('parent_id',null)->get();
?>

@if(count($cats)>0)

    <section class='cats_product'>
        <div class="container">
            <div class="title">Удобный выбор категорий</div>
            <div class="ul_cats_product">
                    @foreach ($cats as $cat)
                        <div class="li_cat_product">
                            <div class="title_news">{{ $cat->title }}</div>
                            <?php $cats_children=childrenCategoryProducts($cat);?>
                            @if (count($cats_children)>0)
                                <div class="ul_children_cat_product">
                                    @foreach ($cats_children as $cat_children)
                                        <a href="{{ route('marketplace.category',$cat_children->slug) }}" class="title_label li_cat_children_product">{{ $cat_children->title }}</a>
                                    @endforeach

                                </div>    
                            @endif
                            @if (count($cats_children)>2)
                                <div class="white_btn_blue_text more_children_cats_product">Показать еще</div>
                            @endif
                        </div>
                    @endforeach
                
                
                
            </div>
            <div class="see_all_cats_product">
                Смотреть все<img src="{{ asset('storage/uploads/Arrow.svg') }}" alt="">
            </div>
            <a href='{{ route('marketplace') }}' class="blue_btn">Смотреть все товары</a>
        </div>
    </section>
@endif
