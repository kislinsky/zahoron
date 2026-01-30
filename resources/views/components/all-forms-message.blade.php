
@include('forms.beautification-burial')
@include('forms.memorial-form')
@include('forms.city')
@include('forms.dead')
@include('forms.call-organization')
@include('forms.cemetery-choose')
@include('forms.funeral-services')
@include('forms.contacts-modal-form')


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


@if(session("success"))
    <div class="bac_black">
        <div class='message'>
            <div class="flex_title_message">
                <div class="title_middle">{!!  session("success") !!} </div>
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


<div type="button" data-bs-toggle="modal" data-bs-target="#feedback_modal" aria-label="Обратная связь" class="open_contact_form">
    <svg viewBox="0 0 24 24" fill="white">
        <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
    </svg>
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


@if(!isset($_COOKIE['cookie_consent']))
    <div id="cookieConsent" class="cookie_consent">
        <div class="cookie-buttons">
            <button id="acceptCookies" value='1' class="blue_btn">Принять</button>
            <button id="rejectCookies" value='0' class="btn_border_blue">Отклонить</button>
        </div>
        <p class='text_black'>Мы используем файлы cookie для улучшения вашего опыта. Принимая, вы соглашаетесь с нашей 
            <a href="{{ route('terms-user') }}">Политикой конфиденциальности</a>.
        </p>
    </div>
@endif

<script>
     $( ".cookie-buttons button" ).on( "click", function() {
        val=$(this).attr('value')
        let filters  = {
            'value':val
        };
        $('#cookieConsent').hide();

        $.ajax({
            type: 'GET',
            url: '{{ route('cookie.accept') }}',
            data: filters,
            success: function (result) {
            },
            error: function () {
            }
        });
    });
</script>