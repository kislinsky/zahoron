
@if($similar_mortuaries!=null && $similar_mortuaries->count()>0)

<section class="block_content_organization_single our_products_single_organization">
    <div class="flex_single_organization">
        <div class="title_li">Похожие морги</div>
    </div>
    <div class="swiper organizations_swiper">
        <div class="swiper-wrapper">
            @foreach($similar_mortuaries as $similar_mortuary)
                <div class="swiper-slide">
                    <div class="li_organization_similar">
                        <img class='logo_organization_similar' src="{{$similar_mortuary->urlImg()}}" alt="">
                        <a href='{{route('mortuary.single',$similar_mortuary->id)}}'class="title_news">{{$similar_mortuary->title}} </a>
                        <div class="flex_stars">
                            <img src="{{asset('storage/uploads/Frame 334.svg')}}" alt="">
                            <div class="text_black_mini">{{$similar_mortuary->rating}}</div>
                        </div>
                        <div class="text_gray">{{$similar_mortuary->adres}}</div>
                    </div>
                </div>
            @endforeach
            
        </div>
      </div>
</section>
@endif