@if (isset($faqs))
    @if ($faqs->count()>0)
        <section class="faq">
            <div class="container">
                <h2 class="title">Часто задаваемые вопросы</h2>
                <div class="ul_faq">
                    @foreach ($faqs as $faq )    
                        <div class="li_faq">
                            <div page='marketplace'class="flex_li_service">
                                <div class="title_li">{!! changeContent($faq->title,$product) !!}</div>
                                <img class='open_faq'src="{{asset('storage/uploads/Переключатель (2).svg')}}" alt="">
                            </div>
                            <div class="text_li">{!! changeContent($faq->content,$product) !!}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
<script>
$( ".li_faq .flex_li_service" ).on( "click", function() {
  $(this).children('.open_faq').toggleClass('open_faq_active')
  $(this).siblings('.text_li').slideToggle()
})
</script>
    @endif
@endif
