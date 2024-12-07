@if($reviews_main!=null && count($reviews_main)>0)
    <div class="block_content_organization_single organization_single_reviews">
        <div class="flex_single_organization">
            <div class="title_li">Отзывы <div class="title_gray">({{count($reviews)}})</div></div>
            <a  class="text_black open_all_reviews_organization">Все отзывы <img src="{{asset('storage/uploads/Vector 9_2.svg')}}" alt=""></a>
        </div>

        <div class="ul_reviews_organization">
            @foreach ($reviews_main as $review)
                <div class="li_review_organization_single_page">
                    <div class="flex_single_organization">
                        <div class="title_rewies">{{$review->name}}</div>
                        <div class="text_black">{{$review->created_at->format('d.m.Y')}}</div>
                    </div>
                    @if($review->rating==5 || ($review->rating!=null && $review->rating!=0))
                        <div class="flex_single_organization">
                            @if($review->rating==5)
                                <div class="text_black reccomend_review">
                                    Рекомендую <img src="{{asset('storage/uploads/mdi_heart.svg')}}" alt="">
                                </div>
                            @endif
                            @if($review->rating!=null && $review->rating!=0)
                                <div class="flex_stars">
                                    @for ($i = 1; $i <= $review->rating; $i++) 
                                        <img src="{{asset('storage/uploads/Frame 334.svg')}}" alt="">
                                    @endfor
                                </div>
                            @endif
                        </div>
                    @endif
                    <div class="text_black margin_top_review_organization">{{$review->content}}</div>
                    @if($review->organization_response!=null)
                        {!!alert($review->organization_response)!!}
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif