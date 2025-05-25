
@if($similar_columbariums!=null && $similar_columbariums->count()>0)

<section class="block_content_organization_single our_products_single_organization">
    <div class="flex_single_organization">
        <div class="title_li">Похожие колумбарии</div>
    </div>
    <div class="swiper organizations_swiper">
        <div class="swiper-wrapper">
            @foreach($similar_columbariums as $similar_columbarium)
                <div class="swiper-slide">
                    <div class="li_organization_similar">
                            <img class='white_img_org logo_organization_similar' src="{{$similar_columbarium->defaultImg()[0]}}" alt="">   
                            <img class='black_img_org logo_organization_similar' src="{{$similar_columbarium->defaultImg()[1]}}" alt="">   
                     
                        <a href='{{route('crematorium.single',$similar_columbarium->id)}}'class="title_news">{{$similar_columbarium->title}} </a>
                        <div class="flex_stars">
                            <img src="{{asset('storage/uploads/Frame 334.svg')}}" alt="">
                            <div class="text_black_mini">{{$similar_columbarium->rating}}</div>
                        </div>
                        <div class="text_gray">{{$similar_columbarium->adres}}</div>
                    </div>
                </div>
            @endforeach
            
        </div>
      </div>
</section>
@endif