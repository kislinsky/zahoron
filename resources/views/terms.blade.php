@include('header.header')

<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <div class="index_title">Пользовательское соглашение</div>    
        </div>
        <img class='img_light_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        <img class='img_black_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1_black.svg')}}" alt="">
    </div>
</section>

<section class="contacts">
    <div class="container">
        <div class="text_black">        
            {!! $content !!}
        </div>
    </div>
</section>




@include('footer.footer') 