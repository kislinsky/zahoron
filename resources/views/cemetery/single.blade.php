@include('header.header')
{{view('components.shema-org.ritual-object',compact('cemetery'))}}


<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <h1 class="index_title"> {{ $title_h1 }}</h1>    
        </div>
        <img class='img_light_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        <img class='img_black_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1_black.svg')}}" alt="">        
    </div>
</section>

{{view('components.navigation',compact('pages_navigation'))}}


<section class="organization_single">
    <div class="container">
        <div class="grid_organization_single">
            <div class="main_content_organization_single">

                {{view('cemetery.components.single.main-block',compact('cemetery','reviews'))}}


                <div class="block_menu_single_organization">
                    <div id_block=1 class="menu_single_organization menu_single_organization_active">
                        <div class="text_black">О нас</div>
                    </div>
                    {{-- <div id_block=2 class="menu_single_organization">
                        <div class="text_black">Цены</div>
                    </div> --}}
                   
                    <div id_block=3 class="menu_single_organization">
                        <div class="text_black">Фотогалерея</div>
                    </div>
                   
                    <div id_block=4 id='title_block_2'class="menu_single_organization">
                        <div class="text_black">Отзывы</div>
                    </div>
                </div>
                
                <div id_block=1 class="flex_block_single_organization flex_block_single_organization_active">

                    
                    @if($cemetery->content!=null)
                        <div class="block_content_organization_single">
                            <h2 class="title_li title_li_organization_single">О нас</h2>
                            <div class="text_black">
                                <div class="content_not_all">{!!custom_echo(contentCart($cemetery->content,$cemetery),450)!!}</div>
                                <div class="content_all">{!!contentCart($cemetery->content,$cemetery)!!}</div>
                            </div>
                        </div>
                    @endif
                    {{view('cemetery.components.single.specifications',compact('cemetery'))}}

                    {{view('cemetery.components.single.our-organizations',compact('organizations_our'))}}


                    {{view('cemetery.components.single.reviews_main',compact('reviews_main','reviews'))}}

                    {{view('cemetery.components.single.add-reviews',compact('cemetery'))}}

                    {{view('cemetery.components.single.services',compact('services','cemetery','city'))}}

                    {{view('cemetery.components.single.faq',compact('faqs','city','cemetery'))}}

                    {{view('cemetery.components.single.map',compact('similar_cemeteries','cemetery'))}}

                    {{view('cemetery.components.single.characteristics',compact('characteristics','cemetery'))}}
                   
                    {{view('cemetery.components.single.similar-cemeteries',compact('similar_cemeteries'))}}
                    

                </div>
                

                <div id_block=2  id='block_prices' class="flex_block_single_organization">
                    {{view('cemetery.components.single.services',compact('services','cemetery','city'))}}

                </div>

                <div id_block=3  id='block_gallery' class="flex_block_single_organization">
                    {{view('cemetery.components.single.gallery',compact('images'))}}

                </div>

                <div id_block=4  id='block_reviews' class="flex_block_single_organization">
                    {{view('cemetery.components.single.reviews',compact('reviews'))}}
                    @php $add_review_second = true; @endphp
                    {{view('cemetery.components.single.add-reviews',compact('cemetery','add_review_second'))}}
                </div>
       

        </div>    
            
            <div class="sidebar">
                {{view('cemetery.components.single.sidebar',compact('cemetery'))}}
            </div>
        </div>
        
    </div>
</section>

@include('components.ritual-objects') 




@include('footer.footer')
