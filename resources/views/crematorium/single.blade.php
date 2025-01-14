@include('header.header')
{{view('components.shema-org.ritual-object',compact('crematorium'))}}

<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <h1 class="index_title"> {{ $title_h1 }}</h1>    
        </div>
        <img class='img_light_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        <img class='img_black_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1_black.svg')}}" alt="">        
    </div>
</section>

<section class="organization_single">
    <div class="container">
        <div class="grid_organization_single">
            <div class="main_content_organization_single">

                {{view('crematorium.components.single.main-block',compact('crematorium','reviews'))}}


                <div class="block_menu_single_organization">
                    <div id_block=1 class="menu_single_organization menu_single_organization_active">
                        <div class="text_black">О нас</div>
                    </div>
                    <div id_block=2 class="menu_single_organization">
                        <div class="text_black">Цены</div>
                    </div>
                   
                    <div id_block=3 class="menu_single_organization">
                        <div class="text_black">Фотогалерея</div>
                    </div>
                   
                    <div id_block=4 id='title_block_2'class="menu_single_organization">
                        <div class="text_black">Отзывы</div>
                    </div>
                </div>
                
                <div id_block=1 class="flex_block_single_organization flex_block_single_organization_active">

                    
                    @if($crematorium->content!=null)
                        <div class="block_content_organization_single">
                            <div class="title_li title_li_organization_single">О нас</div>
                                <div class="text_black">
                                    <div class="content_not_all">{!!custom_echo($crematorium->content,450)!!}</div>
                                    <div class="content_all">{!!$crematorium->content!!}</div>
                                </div>
                            </div>
                    @endif
                    {{view('crematorium.components.single.specifications',compact('crematorium'))}}

                    {{view('crematorium.components.single.our-organizations',compact('organizations_our'))}}

                    {{view('crematorium.components.single.reviews_main',compact('reviews_main','reviews'))}}

                    {{view('crematorium.components.single.add-reviews',compact('crematorium'))}}

                    {{view('crematorium.components.single.services',compact('services','crematorium','city'))}}

                    {{view('crematorium.components.single.faq',compact('faqs','city','crematorium'))}}

                    {{view('crematorium.components.single.map',compact('crematorium_all','crematorium'))}}

                    {{view('crematorium.components.single.characteristics',compact('characteristics','crematorium'))}}
                   
                    {{view('crematorium.components.single.similar-crematoriums',compact('similar_crematoriums'))}}
                    

                </div>

                <div id_block=2  id='block_prices' class="flex_block_single_organization">
                    {{view('crematorium.components.single.services',compact('services','crematorium','city'))}}

                </div>

                <div id_block=3  id='block_gallery' class="flex_block_single_organization">
                    {{view('crematorium.components.single.gallery',compact('images'))}}

                </div>

                <div id_block=4  id='block_reviews' class="flex_block_single_organization">
                    {{view('crematorium.components.single.reviews',compact('reviews'))}}
                    @php $add_review_second = true; @endphp
                    {{view('crematorium.components.single.add-reviews',compact('crematorium','add_review_second'))}}
                </div>
       

        </div>    
            
            <div class="sidebar">
                {{view('crematorium.components.single.sidebar',compact('crematorium'))}}
            </div>
        </div>
        
    </div>
</section>


@include('components.ritual-objects') 

@include('footer.footer')
