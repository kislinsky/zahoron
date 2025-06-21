

@if (isset($faqs))
    @if (count($faqs)>0)
        <section class="faq_organization">
            <h2 class="title_li">Популярные вопросы по крематорию {{$crematorium->title}} в г. {{$city->title}}</h2>
            <div class="ul_faq">
            
                    @foreach ($faqs as $faq )    
                        <div class="li_faq">
                            <div class="flex_li_service">
                                <div class="title_li">{{ $faq->title }}</div>
                                <img class='open_faq'src="{{asset('storage/uploads/Переключатель (2).svg')}}" alt="">
                            </div>
                            <div class="text_li">{{ changeContent($faq->content) }}</div>
                        </div>
                    @endforeach
            
            </div>
        </section>
    @endif
@endif