
@if($organizations_our!=null && $organizations_our->count()>0)

<section class="block_content_organization_single our_products_single_organization">
    <div class="flex_single_organization">
        <div class="title_li">Ритуальные агенства</div>
    </div>
    <div class="swiper our_products_swiper">
        <div class="swiper-wrapper">
            @foreach($organizations_our as $organization_our)
                <div class="swiper-slide">
                    <div class="li_organization_similar">
                       @if($organization_our->urlImg()=='default')
                            <img class='white_img_org logo_organization_similar' src="{{$organization_our->defaultLogoImg()[0]}}" alt="">   
                            <img class='black_img_org logo_organization_similar' src="{{$organization_our->defaultLogoImg()[1]}}" alt="">   
                        @else
                            <img  class='logo_organization_similar' src="{{$organization_our->urlImg()}}" alt="">   
                        @endif
                        <a href='{{$organization_our->route()}}'class="title_news">{{$organization_our->title}} </a>
                        <div class="flex_stars">
                            <img src="{{asset('storage/uploads/Frame 334.svg')}}" alt="">
                            <div class="text_black_mini">{{$organization_our->rating}}</div>
                        </div>
                        <div class="text_gray">{{$organization_our->adres}}</div>
                    </div>
                </div>
            @endforeach
            
        </div>
      </div>
</section>
@endif