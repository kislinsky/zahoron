
@if($similar_crematoriums!=null && $similar_crematoriums->count()>0)

<section class="block_content_organization_single our_products_single_organization">
    <div class="flex_single_organization">
        <h2 class="title_li">Похожие крематории</h2>
    </div>
    <div class="swiper organizations_swiper">
        <div class="swiper-wrapper">
            @foreach($similar_crematoriums as $similar_crematorium)
                <div class="swiper-slide">
                    <div class="li_organization_similar">
                            <img class='white_img_org logo_organization_similar' src="{{$similar_crematorium->defaultImg()[0]}}" alt="">   
                            <img class='black_img_org logo_organization_similar' src="{{$similar_crematorium->defaultImg()[1]}}" alt="">   
                       
                        <a href='{{route('crematorium.single',$similar_crematorium->id)}}'class="title_news">{{$similar_crematorium->title}} </a>
                        <div class="flex_stars">
                            <img src="{{asset('storage/uploads/Frame 334.svg')}}" alt="">
                            <div class="text_black_mini">{{$similar_crematorium->rating}}</div>
                        </div>
                        <div class="text_gray">{{$similar_crematorium->adres}}</div>
                    </div>
                </div>
            @endforeach
            
        </div>
      </div>
</section>
@endif