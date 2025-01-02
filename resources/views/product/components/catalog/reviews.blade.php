@if($reviews->count()>0)
<section class='reviews_organizations reviews_products'>
    <div class="container">
        <div class="title">Отзывы клиентов</div>
            <div class="swiper reviews_funeral_agencies_swiper">
                <div class="swiper-wrapper">
                @foreach($reviews as $review)
                    <div class="swiper-slide">
                        <div class="li_review_organization">
                            <div class='name_organization'>
                                <a href={{$review->product->route()}} class="title_organization">"{{$review->product->title}}"</a>
                            </div>
                            <div class="content_block">
                                <div class="content_not_all">{!!custom_echo($review->content,200)!!}</div>
                                <div class="content_all">{!!$review->content!!}</div>
                            </div>
                            <div class="text_li">
                                {{$review->name}} 
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>
            </div>

            <div class="swiper-button-next swiper_button_next_reviews_funeral_agencies"><img src='{{asset('storage/uploads/Переключатель.svg')}}'></div>
            <div class="swiper-button-prev swiper_button_prev_reviews_funeral_agencies"><img src='{{asset('storage/uploads/Переключатель (1).svg')}}'></div>
    </div>
</section>
@endif


<script>
    $( ".open_all_content_block" ).on( "click", function() {
  $(this).parent().hide()
  $(this).parent().siblings('.content_all').show()
  
})
</script>