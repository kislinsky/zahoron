@include('header.header-decoder')


<div class="account_container">
    <div class="flex_account">
        @include('account.decoder.components.sidebar')
        <div class="container_content_account">
            <div class="container">
                <div class="title_middle">На проверке</div>   
                <div class="text_black">Оплачивается минимальное количество расшифровок в количестве 500 штук в течение 3-5 дней после запроса на вывод средств</div>          
                <div class="ul_payments_decoder">
                    @if($payments!=null && $payments->count()>0)
                        @foreach($payments as $payment)
                        <div action="" class="withdraw_decoder">
                            <div class="li_payment_decoder">
                                <div class="text_black_big">Оплата №{{$payment->id}} от {{$payment->created_at->format('d.m.Y')}} г. {{$payment->title}} {{$payment->count}} шт.</div>
                                <div class="text_black_big_bold">{{$payment->price}} руб.</div>
                            </div>
                            @if($payment->status==0)
                                <a href='{{route('account.decoder.withdraw',$payment->id)}}' class="blue_btn">Вывести </a>
                            @endif
                            @if($payment->status==2)
                                <div class="blue_btn">На проверке</div>
                            @endif
                        </div>
                        @endforeach
                    @endif  
                </div>      
            </div>
        </div>
    </div>
</div>

@include('footer.footer')