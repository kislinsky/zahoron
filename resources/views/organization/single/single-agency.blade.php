@include('header.header')

<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <div class="index_title">Организация: {{$organization->title}} в г. {{$city->title}}</div>    
        </div>
        <img class='rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        
    </div>
</section>


<section class="organization_single">
    <div class="container">
        <div class="grid_organization_single">
            <div class="main_content_organization_single">

                {{view('organization.components.main-block',compact('organization','rating_reviews','reviews'))}}


                <div class="block_menu_single_organization">
                    <div id_block=1 class="menu_single_organization menu_single_organization_active">
                        <div class="text_black">О нас</div>
                    </div>
                    <div id_block=5 class="menu_single_organization">
                        <div class="text_black">Цены</div>
                    </div>
                   
                    <div id_block=4 class="menu_single_organization">
                        <div class="text_black">Фотогалерея</div>
                    </div>
                   
                    <div id_block=2 id='title_block_2'class="menu_single_organization">
                        <div class="text_black">Отзывы</div>
                    </div>
                </div>
                
                <div id_block=1 class="flex_block_single_organization flex_block_single_organization_active">

                    
                    @if($organization->content!=null)
                    <div class="block_content_organization_single">
                        <div class="title_li title_li_organization_single">О нас</div>
                            <div class="text_black">
                                <div class="content_not_all">{!!custom_echo($organization->content,450)!!}</div>
                                <div class="content_all">{!!$organization->content!!}</div>
                            </div>
                        </div>
                    
                    @endif

                    {{view('organization.components.specifications',compact('organization','categories_organization'))}}

                    {{view('organization.components.our-products',compact('products_our'))}}

                    {{view('organization.components.reviews_main',compact('reviews_main','reviews'))}}
                    
                    {{view('organization.components.add-reviews',compact('organization'))}}

                    {{view('organization.components.ritual-products',compact('ritual_products'))}}

                    {{view('organization.components.faq',compact('organization'))}}

                    {{view('organization.components.map',compact('organization_all','organization'))}}


                    <div class="block_content_organization_single info_about_organization">
                        <div class="title_li title_li_organization_single">ООО “Название компании”</div>
                        <div class="text_black">Юридический адрес ул. Филимонова, 22 пом. 132</div>
                        <div class="text_black">ИНН: 192493420125</div>
                        <div class="text_black">Свидетельство регистрации от 17.07.2015</div>
                        <div class="text_black">Регистрирующий орган: Нзвание органа</div>
                        <div class="text_black">ЮЗарегистрирован в Реестре бытовых услуг 28.11.2017, номер 0000000737722</div>
                    </div>

                    {{view('organization.components.similar-organizations',compact('similar_organizations'))}}

                </div>

                <div id_block=2  id='block_reviews' class="flex_block_single_organization">
                    {{view('organization.components.reviews',compact('reviews'))}}
                    @php $add_review_second = true; @endphp
                    {{ view('organization.components.add-reviews',compact('organization', "add_review_second"))}}
                </div>

                <div id_block=4   class="flex_block_single_organization">
                    {{view('organization.components.gallery',compact('images'))}}
                </div>

                <div id_block=5   class="flex_block_single_organization block_ritual_products">
                    {{view('organization.components.min-price-ritual-services',compact('organization','categories_organization'))}}
                    
                    {{view('organization.components.products-services-organization',compact('organization','ritual_products','main_categories','children_categories'))}}

                </div>


        </div>    
            
            <div class="sidebar">
                {{view('organization.components.sidebar',compact('organization'))}}
            </div>
        </div>
        
    </div>
</section>

@include('footer.footer')