@if($reviews->count()>0)

<div class="ul_reviews_organiaztions">
    @foreach ($reviews as $review)
        <div class="li_review_in_panel">
            <div class="flex_info_review_in_panel">
                <div class="info_review">
                    <div class="text_black_bold text_align_start">{{$review->product->title}}</div>
                    <div class="text_black">{{$review->created_at->format('j F Y');}}</div>
                </div>
                
                {!!btnStatusReview($review->status)!!}
            </div>
            
            <div class="flex_single_organization">
                <div class="title_rewies">{{$review->name}}</div>
                @if($review->rating!=null && $review->rating!=0)
                    <div class="flex_stars">
                        @for ($i = 1; $i <= $review->rating; $i++) 
                            <img src="{{asset('storage/uploads/Frame 334.svg')}}" alt="">
                        @endfor
                    </div>
                @endif
            </div>
           
            <div class="text_black">
                <div class="content_not_all">{!!custom_echo($review->content,200)!!}</div>
                <div class="content_all">{!!$review->content!!}</div>
            </div>
            
            <div class="flex_single_organization">
                <div class="flex_btn">
                    {!!$review->btnReviewAccept()!!}
                    <div id_review='{{$review->id}}' content_resonse='{{$review->organization_response}}' class="gray_btn open_review_update_organization_response_form">Ответить</div>
                </div>
                <a href='{{route('account.agency.review.product.delete',$review->id)}}'class="delete_product_organization"><img src="{{asset('storage/uploads/Vector (16).svg')}}" alt=""></a>

            </div>
        </div>        
    @endforeach
    {{ $reviews->withPath(route('account.agency.reviews.product'))->appends($_GET)->links() }}

</div>

{{view('account.agency.components.reviews.form-update-review-product')}}
{{view('account.agency.components.reviews.form-update-review-product-organization-response')}}

@else
<div class="text_black">Нет отзывов</div>
@endif