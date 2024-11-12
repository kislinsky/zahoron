@include('header.header-account')
<?php 
    use App\Models\Burial;
    use App\Models\Service;
?>
<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <div class="index_title">Поиск могил</div>    
        </div>
        <img class='rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        
    </div>
</section>


<section class="orders">
    <div class="container">

        <div class="flex_titles_account">
            @if (isset($status))
                <a href='{{ route('account.burial-request.filter',0) }}'class="btn_bac_gray<?php if($status==0){echo ' active_label_product';}?>">В работе </a>
                <a href='{{ route('account.burial-request.filter',2) }}'class="btn_bac_gray<?php if($status==2){echo ' active_label_product';}?>">Найдено </a>
                <a href='{{ route('account.burial-request.filter',1) }}'class="btn_bac_gray<?php if($status==1){echo ' active_label_product';}?>">Отказ </a>
            
            @else
                <a href='{{ route('account.burial-request.filter',0) }}'class="btn_bac_gray">В работе </a>
                <a href='{{ route('account.burial-request.filter',2) }}'class="btn_bac_gray">Найдено </a>
                <a href='{{ route('account.burial-request.filter',1) }}'class="btn_bac_gray">Отказ </a>
            @endif
            
        </div>

        <div class="ul_orders">
        @if(isset($search_burial))
            @if(count($search_burial)>0)
                @foreach ($search_burial as $search_burial)
                    <div class="li_order">
                        <div class="title_li decoration_on">{{ $search_burial->surname }} {{ $search_burial->name }} {{ $search_burial->patronymic }}</div>
                        <div class="mini_flex_li_product">
                            <div class="title_label">Даты захоронения:</div>
                            <div class="text_li">{{ $search_burial->date_birth }}-{{ $search_burial->date_death }}</div>
                        </div>
                        <div class="mini_flex_li_product">
                            <div class="title_label">Место захоронения:</div>
                            <div class="text_li">{{ $search_burial->location }}</div>
                        </div>
                     
                        @if($search_burial->status==0)
                            <div class="light_blue_btn">В работе</div>
                        @elseif ($search_burial->status==1)
                            <div class="red_btn">Отказ</div>
                        @elseif ($search_burial->status==2)
                            <div class="green_btn">Исполнено</div>
                        @endif


                        @if($search_burial->status==2)
                            <div class="blue_btn">Оплатить</div>
                        @endif
                        
                    </div>
                @endforeach
           
            @endif
        @endif
        </div>
    </div>
</section>
@include('footer.footer') 