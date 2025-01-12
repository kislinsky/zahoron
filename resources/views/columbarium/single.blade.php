@include('header.header')

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

                {{view('columbarium.components.single.main-block',compact('columbarium','reviews'))}}


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

                    
                    @if($columbarium->content!=null)
                        <div class="block_content_organization_single">
                            <div class="title_li title_li_organization_single">О нас</div>
                                <div class="text_black">
                                    <div class="content_not_all">{!!custom_echo($columbarium->content,450)!!}</div>
                                    <div class="content_all">{!!$columbarium->content!!}</div>
                                </div>
                            </div>
                    @endif
                    {{view('columbarium.components.single.specifications',compact('columbarium'))}}

                    {{view('columbarium.components.single.our-organizations',compact('organizations_our'))}}

                    {{view('columbarium.components.single.reviews_main',compact('reviews_main','reviews'))}}

                    {{view('columbarium.components.single.add-reviews',compact('columbarium'))}}

                    {{view('columbarium.components.single.services',compact('services','columbarium','city'))}}

                    {{view('columbarium.components.single.faq',compact('faqs','city','columbarium'))}}

                    {{view('columbarium.components.single.map',compact('columbarium_all','columbarium'))}}

                    {{view('columbarium.components.single.characteristics',compact('characteristics','columbarium'))}}
                   
                    {{view('columbarium.components.single.similar-columbariums',compact('similar_columbariums'))}}
                    

                </div>

                <div id_block=2  id='block_prices' class="flex_block_single_organization">
                    {{view('columbarium.components.single.services',compact('services','columbarium','city'))}}

                </div>

                <div id_block=3  id='block_gallery' class="flex_block_single_organization">
                    {{view('columbarium.components.single.gallery',compact('images'))}}

                </div>

                <div id_block=4  id='block_reviews' class="flex_block_single_organization">
                    {{view('columbarium.components.single.reviews',compact('reviews'))}}
                    @php $add_review_second = true; @endphp
                    {{view('columbarium.components.single.add-reviews',compact('columbarium','add_review_second'))}}
                </div>
       

        </div>    
            
            <div class="sidebar">
                {{view('columbarium.components.single.sidebar',compact('columbarium'))}}
            </div>
        </div>
        
    </div>
</section>

@include('components.ritual-objects') 


@include('footer.footer')
