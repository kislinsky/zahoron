@if (isset($usefuls))
    @if (count($usefuls)>0)
        <section class="faq">
            <div class="container">
                <div class="title">Будет полезно</div>
                <div class="ul_faq">
                        @foreach ($usefuls as $useful )    
                            <div class="li_faq">
                                <div class="flex_li_service">
                                    <div class="title_li">{{ changeContent($useful->title) }}</div>
                                    <img class='open_faq'src="{{asset('storage/uploads/Переключатель (2).svg')}}" alt="">
                                </div>
                                <div class="text_li">{{ changeContent($useful->content) }}</div>
                            </div>
                        @endforeach
                </div>
            </div>
        </section>
    @endif
@endif