
@if($similar_cemeteries!=null && $similar_cemeteries->count()>0)

<section class="block_content_organization_single our_products_single_organization">
    <div class="flex_single_organization">
        <h2 class="title_li">Похожие кладбища</h2>
    </div>
    <div class="swiper organizations_swiper">
        <div class="swiper-wrapper">
            @foreach($similar_cemeteries as $similar_cemetery)
                <div class="swiper-slide">
                    <div class="li_organization_similar">
                        <img class='white_img_org logo_organization_similar' src="{{$similar_cemetery->defaultImg()[0]}}" alt="">   
                        <img class='black_img_org logo_organization_similar' src="{{$similar_cemetery->defaultImg()[1]}}" alt="">   
                       
                        <a href='{{route('cemeteries.single',$similar_cemetery->id)}}'class="title_news">{{$similar_cemetery->title}} </a>
                        <div class="flex_stars">
                            <img src="{{asset('storage/uploads/Frame 334.svg')}}" alt="">
                            <div class="text_black_mini">{{$similar_cemetery->rating}}</div>
                        </div>
                        <div class="text_gray">{{$similar_cemetery->adres}}</div>
                    </div>
                </div>
            @endforeach
            
        </div>
      </div>
</section>
@endif