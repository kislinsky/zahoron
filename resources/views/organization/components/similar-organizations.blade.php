
@if($similar_organizations!=null && $similar_organizations->count()>0)

<section class="block_content_organization_single our_products_single_organization">
    <div class="flex_single_organization">
        <h2 class="title_li">Похожие компании</h2>
        <a href='{{route('organizations')}}' class="text_black">Все компании <img src="{{asset('storage/uploads/Vector 9_2.svg')}}" alt=""></a>
    </div>
    <div class="swiper organizations_swiper">
        <div class="swiper-wrapper">
            @foreach($similar_organizations as $similar_organization)
                <div class="swiper-slide">
                    <div class="li_organization_similar">
                        @if($similar_organization->urlImg()=='default')
                            <img class='white_img_org logo_organization_similar' src="{{$similar_organization->defaultLogoImg()[0]}}" alt="">   
                            <img class='black_img_org logo_organization_similar' src="{{$similar_organization->defaultLogoImg()[1]}}" alt="">   
                        @else
                            <img  class='logo_organization_similar' src="{{$similar_organization->urlImg()}}" alt="">   
                        @endif
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