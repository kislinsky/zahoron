<?php 

use App\Models\Faq;
$faqs=Faq::orderBy('id','desc')->get();
?>
@if (isset($faqs))
    @if (count($faqs)>0)
        <section class="faq">
            <div class="container">
                <div class="ul_faq">
                
                        @foreach ($faqs as $faq )    
                            <div class="li_faq">
                                <div class="flex_li_service">
                                    <div class="title_li">{!! changeContent($faq->title) !!}</div>
                                    <img class='open_faq'src="{{asset('storage/uploads/Переключатель (2).svg')}}" alt="">
                                </div>
                                <div class="text_li">{!! changeContent($faq->content) !!}</div>
                            </div>
                        @endforeach
                
                </div>
            </div>
        </section>
    @endif
@endif