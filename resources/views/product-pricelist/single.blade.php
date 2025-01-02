@include('header.header')

<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <div class="index_title">{{$product->title}} в городе <a href="{{route('city.select',$city->id)}}">{{$city->title}}</a></div>    
        </div>
        <img class='rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        
    </div>
</section>

<section class="product_price_list">
    <div class="container">
        @if($product->title_advice!=null)
            <div class="title">{{$product->title_advice}} </div>
        @endif
       
        @if(count($advices)>0)
            <div class="ul_advice">
                @foreach($advices as $advice)
                    <div class="li_advice">
                        <img src="{{asset('storage/uploads_product_price_list/'.$advice->img)}}" alt="">
                        <div class="title_advice">{{$advice->title}}</div>
                    </div>
                @endforeach
            </div>
        @endif


        @if($product->content!=null)
            <div class="text_block border_gray">
                {!! $product->content !!}
            </div>
        @endif
        
        @if(count($advantages)>0)
            <div class="ul_advanyages_service">
                @foreach($advantages as $advantage)
                    <div class="li_advantage_service">
                        <img src="{{asset('storage/uploads_product_price_list/'.$advantage->img)}}" alt="">
                        <div class="title_news">{{$advantage->title}}</div>
                        <div class="title_rewies">{{$advantage->content}}</div>
                    </div>
                @endforeach
            </div>
        @endif

        @if($product->text_before_video_1!=null)
            <div class="text_block">
                {!! $product->text_before_video_1 !!}
            </div>
        @endif

        @if($product->video_1!=null)
            <div class="video_service">
                <img class='btn_play_video' src="{{asset('storage/uploads/Group 34.svg')}}" alt="">
                <video controls src="{{asset('storage/uploads_product_price_list/'. $product->video_1 )}}"></video>
            </div>
        @endif


        @if($product->text_after_video_1!=null)
            <div class="text_block">
                {!! $product->text_after_video_1 !!}
            </div>
        @endif

        @if($product->text_before_videos!=null)
            <div class="videos_products_price_list">
                <div class="title">Видео {{$product->title}}</div>
                
                    <div class="text_block">
                        {!! $product->text_before_videos !!}
                    </div>

                @if($product->text_after_videos!=null)
                    <div class="text_block border_gray">
                        {!! $product->text_after_videos !!}
                    </div>
                @endif
            </div>
        @endif

        <div class="videos_products_price_list">
            @if($product->text_images!=null || count($imgs_service)>0)
                <div class="title">Фотографии работ До и После</div>
                    <div class="text_block">
                        {!! $product->text_images !!}
                    </div>
            @endif

            @if (count($imgs_service)>0)
                <div class="ul_our_products">
                    @foreach ($imgs_service as $img_service )
                        <div class="li_our_work">
                            <div class="title_before_our_works">До уборки</div>
                            <div class="title_after_our_works">После уборки</div>
                            <img src="{{asset('storage/uploads_product_price_list/'. $img_service->img_before )}}" alt="">
                            <img src="{{asset('storage/uploads_product_price_list/'. $img_service->img_after )}}" alt="">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        

        

        {{view('product-pricelist.components.reviews',compact('reviews'))}}

        @if($product->text_advantages!=null)
            <div class="text_block border_gray">
                <div class="title">Чем хорош {{$product->title}}</div>
                {!! $product->text_advantages !!}
            </div>
        @endif

      
        @if($product->video_2!=null)
            <div class="video_service">
                <img class='btn_play_video' src="{{asset('storage/uploads/Group 34.svg')}}" alt="">
                <video controls src="{{asset('storage/uploads_product_price_list/'. $product->video_2 )}}"></video>
            </div>
        @endif

        <div class="single_flex_btn">
            <div class="blue_btn" data-bs-toggle="modal" data-bs-target="#beautification_form">Заказать {{$product->title}}</div>
        </div>
        @if($product->text_how_make!=null)
            <div class="text_block">
                <div class="title">Как создаётся {{$product->title}}?</div>
                <p>{!! $product->text_how_make !!}</p>


                @if(count($stages)>0)
                    <?php $k=1;?>
                    <div class="ul_stages_product_price_list">
                        @foreach($stages as $stage)
                            <div class="li_stage_product_price_list">
                                <div class="number_li_stage">{{$k++}}</div>
                                <div class="content_stage">
                                    <div class="title_news">{{$stage->title}}</div>
                                    <div class="text_block">{!!$stage->content!!}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        @endif
        

        @if($product->title_variants!=null)
            <div class="block_variants">
                <div class="title">{{$product->title_variants}}</div>
                <div class="text_block">{!! $product->text_variants !!}</div>
                @if(count($variants)>0)
                    <div class="ul_variants">
                        @foreach($variants as $variant)
                            <div class="li_variant">
                                <img src="{{asset('storage/uploads_product_price_list/'. $variant->img )}}" alt="">
                                <div class="title_li">{{$variant->title}}</div>
                            </div>
                        @endforeach
                    </div>
                @endif            
            </div>
        @endif            

        <div class="videos_products_price_list">
            @if($product->text_images!=null || count($imgs_service)>0)
                <div class="title">Фотографии работ До и После</div>
                    <div class="text_block">
                        {!! $product->text_images !!}
                    </div>
            @endif
           

            @if (count($imgs_service)>0)
                <div class="ul_our_products">
                    @foreach ($imgs_service as $img_service )
                        <div class="li_our_work">
                            <div class="title_before_our_works">До уборки</div>
                            <div class="title_after_our_works">После уборки</div>
                            <img src="{{asset('storage/uploads_product_price_list/'. $img_service->img_before )}}" alt="">
                            <img src="{{asset('storage/uploads_product_price_list/'. $img_service->img_after )}}" alt="">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{view('product-pricelist.components.faq',compact('faqs'))}}
        @include('forms.search-form') 

        @include('components.cats-product-price-list') 
    </div>
</section>



@include('footer.footer')
