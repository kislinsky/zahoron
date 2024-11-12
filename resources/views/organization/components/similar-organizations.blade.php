
@if($similar_organizations!=null && $similar_organizations->count()>0)

<section class="block_content_organization_single our_products_single_organization">
    <div class="flex_single_organization">
        <div class="title_li">Похожие компании</div>
        <a href='#' class="text_black">Все компании <img src="{{asset('storage/uploads/Vector 9_2.svg')}}" alt=""></a>
    </div>
    <div class="swiper organizations_swiper">
        <div class="swiper-wrapper">
            @foreach($similar_organizations as $similar_organization)
                <div class="swiper-slide">
                    <div class="li_organization_similar">
                        <img class='logo_organization_similar'src="{{asset($similar_organization->urlImg())}}" alt="">
                        <a href='{{$similar_organization->route()}}'class="title_news">{{$similar_organization->title}} </a>
                        <div class="flex_stars">
                            <img src="{{asset('storage/uploads/Frame 334.svg')}}" alt="">
                            <div class="text_black_mini">{{$similar_organization->rating}}</div>
                        </div>
                        <div class="text_gray">{{$similar_organization->adres}}</div>
                    </div>
                </div>
            @endforeach
            
        </div>
      </div>
</section>
@endif