@include('header.header-account')
<?php 
    use App\Models\Burial;
    use App\Models\Service;
?>
<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <div class="index_title">Здравствуйте, {{ $user->name }}! <br>Добро пожаловать в ваш личный кабинет.</div>    
        </div>
        <img class='rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        
    </div>
</section>


<section class="orders">
    <div class="container">
        <div class="title_middle">Последние заказы</div>
        
        
        </div>
    </div>
</section>

@include('footer.footer') 