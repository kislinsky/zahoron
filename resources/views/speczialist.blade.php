@include('header.header')

<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <div class="index_title">Портфолио Специалиста по Ритуальным Услугам Кислинского Александра Валерьевича</div>    
        </div>
        <img class='rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        
    </div>
</section>


<section class="speczialist">
    <div class="container">
        <div class="title">Портфолио Специалиста по Ритуальным Услугам </div>
        <img src="{{ asset('storage/uploads_acf/'.get_acf(1,'img')) }}" alt="" class="speczialist_index_img">
        <div class="content_page_speczialist">
            {!! get_acf(1,'content') !!}
        </div>
    </div>
</section>

@include('footer.footer') 
