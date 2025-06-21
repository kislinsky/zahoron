@include('header.header')

<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <h1 class="index_title">Цены на товары и услуги по уходу за могилой в городе <a href="{{route('city.select',$city->id)}}">{{$city->title}}</a></h1>    
        </div>
        <img class='img_light_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        <img class='img_black_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1_black.svg')}}" alt="">        
    </div>
</section>


{{view('components.navigation',compact('pages_navigation'))}}


<section class="price_service">

    <div class="container grid_two_page">
        <div class="">
            <div class="ul_services">
                @if (isset($services))
                    @if ($services->count()>0)
                        @foreach ($services as $service )
                            <div class="li_service">
                                <div class="flex_li_service">
                                    <a href='{{route('pricelist.single',$service->slug)}}'class="title_li decoration_on">{{ $service->title }}</a>
                                    <div class="title_li">от {{ $service->getPriceForCity($city->id) }} ₽</div>
                                </div>
                                <div class="text_li">{!! $service->excerpt !!}</div>
                            </div>
                        @endforeach
                    @endif
                @endif
            </div>
        </div>
        <div class="sidebar">
            <div class="btn_border_blue" data-bs-toggle="modal" data-bs-target="#beautification_form"><img src="{{asset('storage/uploads/Frame (20).svg')}}" alt="">Облагородить могилу</div>
            <div class="cats_news">
                <div class="title_news">Категории товаров и услуг</div>

                <div class="ul_cats_marketplace">
                    @if(isset($cats))
                        @if($cats->count()>0)
                            @foreach ($cats as $cat)
                                <div class="main_cat">
            
                                    <div id_category='{{ $cat->id }}' class="li_cat_main_marketplace"><img class='icon_black'src="{{ asset('storage/'.$cat->icon) }}" alt=""> <img class='icon_white'src="{{ asset('storage/'.$cat->icon_white) }}" alt="">{{ $cat->title }}</div>
                                    <?php $cats_children=childrenCategoryProductsPriceList($cat);?>
                                    @if (count($cats_children)>0)
                                        <ul class="ul_childern_cats_marketplace">
                                            @foreach ($cats_children as $cat_children)
                                                <li class='li_cat_children_marketplace <?php if(isset($cat_selected) && $cat_selected!=null){if($cat_selected->id==$cat_children->id){echo 'active_category';}}?>'><a href='{{ $cat_children->route() }}' >{{ $cat_children->title }}</a></li>
                                            @endforeach
                                        </ul>    
                                    @endif
                                    
                                </div>
                            @endforeach
                        @endif
                    @endif
                </div>
                
            </div>
        </div>
    </div>
</section>

@if(isset($cat_selected))
    <section class='bac_gray about_service'>
        <div class="container">
            <div class="title">{{$cat_selected->title}} в городе <a href="{{route('city.select',$city->id)}}">{{$city->title}}</a></div>
            <div class="text_about_service">{!! $cat_selected->content !!}</div>
        </div>
    </section>
@endif
<section class="our_works">
    <div class="container">
        <div class="ul_our_products">
        @if(isset($cat_selected))
            @if($cat_selected->video!=null)
                    <div class="video_service">
                        <img class='btn_play_video' src="{{asset('storage/uploads/Group 34.svg')}}" alt="">
                        <video controls src="{{asset('storage/uploads_cats_product_price_list/'. $cat_selected->video )}}"></video>
                    </div>
            @endif
        @endif
        @if (isset($our_works))
            @if (count($our_works)>0)
                @foreach ($our_works as $our_work )
                    <div class="li_our_work">
                        <div class="title_before_our_works">До уборки</div>
                        <div class="title_after_our_works">После уборки</div>
                        <img src="{{asset('storage/uploads_cats_product_price_list/'. $our_work->img_before )}}" alt="">
                        <img src="{{asset('storage/uploads_cats_product_price_list/'. $our_work->img_after )}}" alt="">
                    </div>
                @endforeach
            @endif
        @endif
        </div>
    </div>
</section>

@if(isset($faqs))
    @if(count($faqs)>0)
        <section class="faq">
            <div class="container">
                <div class="ul_faq">
                    @foreach($faqs as $faq)
                        <div class="li_faq">
                            <div class="flex_li_service">
                                <div class="title_li">{{ changeContent($faq->title) }}</div>
                                <img class='open_faq'src="{{asset('storage/uploads/Переключатель (2).svg')}}" alt="">
                            </div>
                            <div class="text_li">{{ changeContent($faq->content) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endif

@include('forms.search-form') 

@include('components.cats-product-price-list') 

@include('footer.footer') 