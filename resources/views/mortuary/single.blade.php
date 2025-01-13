@include('header.header')
{{view('components.shema-org.ritual-object',compact('mortuary'))}}

<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <h1 class="index_title"> {{ $title_h1 }}</h1>    
        </div>
        <img class='rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        
    </div>
</section>

<section class="organization_single">
    <div class="container">
        <div class="grid_organization_single">
            <div class="main_content_organization_single">

                {{view('mortuary.components.single.main-block',compact('mortuary','reviews'))}}


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

                    
                    @if($mortuary->content!=null)
                        <div class="block_content_organization_single">
                            <div class="title_li title_li_organization_single">О нас</div>
                                <div class="text_black">
                                    <div class="content_not_all">{!!custom_echo($mortuary->content,450)!!}</div>
                                    <div class="content_all">{!!$mortuary->content!!}</div>
                                </div>
                            </div>
                    @endif
                    {{view('mortuary.components.single.specifications',compact('mortuary'))}}

                    {{view('mortuary.components.single.our-organizations',compact('organizations_our'))}}

                    {{view('mortuary.components.single.reviews_main',compact('reviews_main','reviews'))}}

                    {{view('mortuary.components.single.add-reviews',compact('mortuary'))}}

                    {{view('mortuary.components.single.services',compact('services','mortuary','city'))}}

                    {{view('mortuary.components.single.faq',compact('faqs','city','mortuary'))}}

                    {{view('mortuary.components.single.map',compact('mortuary_all','mortuary'))}}

                    {{view('mortuary.components.single.characteristics',compact('characteristics','mortuary'))}}
                   
                    {{view('mortuary.components.single.similar-mortuaries',compact('similar_mortuaries'))}}
                    

                </div>

                <div id_block=2  id='block_prices' class="flex_block_single_organization">
                    {{view('mortuary.components.single.services',compact('services','mortuary','city'))}}

                </div>

                <div id_block=3  id='block_gallery' class="flex_block_single_organization">
                    {{view('mortuary.components.single.gallery',compact('images'))}}

                </div>

                <div id_block=4  id='block_reviews' class="flex_block_single_organization">
                    {{view('mortuary.components.single.reviews',compact('reviews'))}}
                    @php $add_review_second = true; @endphp
                    {{view('mortuary.components.single.add-reviews',compact('mortuary','add_review_second'))}}
                </div>
       

        </div>    
            
            <div class="sidebar">
                {{view('mortuary.components.single.sidebar',compact('mortuary'))}}
            </div>
        </div>
        
    </div>
</section>

@include('components.ritual-objects') 

@include('footer.footer')
