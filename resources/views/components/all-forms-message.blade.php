@include('forms.beautification-burial')
@include('forms.memorial-form')
@include('forms.city')
@include('forms.dead')
@include('forms.cemetery-choose')
@include('forms.funeral-services')




@if(session("message_words_memory"))
    <div class="bac_black">
        <div class='message'>
            <div class="flex_title_message">
                <div class="title_middle">Заявка принята</div>
                <div class="close_message">
                    <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                </div>
            </div>
            <div class="content_message">{!!  session("message_words_memory") !!}  </div>
            <div class="blue_btn close_message">OK</div>
        </div>
    </div>
@endif



@if(session("message_cart"))
    <div class="bac_black">
        <div class='message'>
            <div class="flex_title_message">
                <div class="title_middle">{!!  session("message_cart") !!} </div>
                <div class="close_message">
                    <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                </div>
            </div>
            <div class="blue_btn close_message">OK</div>
        </div>
    </div>
@endif

@if(session("message_order_burial"))
    <div class="bac_black">
        <div class='message'>
            <div class="flex_title_message">
                <div class="title_middle">Заказ оформлен</div>
                <div class="close_message">
                    <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                </div>
            </div>
            <div class="content_message">{{ session("message_order_burial") }}</div>
            <div class="blue_btn close_message">OK</div>
        </div>
    </div>
@endif

@if(session("error"))
    <div class="bac_black">
        <div class='message'>
            <div class="flex_title_message">
                <div class="title_middle">Ошибка</div>
                <div class="close_message">
                    <img src="{{ asset('storage/uploads/close (2).svg') }}" alt="">
                </div>
            </div>
            <div class="content_message">{{ session("error") }}</div>
            <div class="blue_btn close_message">OK</div>
        </div>
    </div>
@endif



<div class="to_top">
    <img src="{{asset('storage/uploads/arrow-top-svgrepo-com.svg')}}" alt="">
</div>

<div class="bac_loader"></div>   
<div class="load_block">
        <div class="load-9">
            <div class="spinner">
            <div class="bubble-1"></div>
            <div class="bubble-2"></div>
            </div>
        </div>
    </div>

