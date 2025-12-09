@include('header.header')
<?php 

use App\Models\OurWork;
$count_projects=count(OurWork::orderby('id','desc')->get());
?>
<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <h1 class="index_title">{!! $title_h1 !!}</h1>    
        </div>
        <img class='img_light_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        <img class='img_black_theme rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1_black.svg')}}" alt="">        
    </div>
</section>


<section class="our_works">
    <div class="container">
        <div class="grid_two_our_works">
            <div class="block_one_our_works">
                <h2 class="title_our_works">Ниже {{$count_projects}} отчета по уборкам,<br>ваш отчет может быть следующим!</h2>
                <div class="text_block_mini">{!! get_acf(19,'content_1') !!}</div>
            </div>
            <div class="block_one_our_works">
                <video controls src="{{ asset('storage/'.get_acf(19,'video')) }}"></video>
            </div>
        </div>
        @if (isset($cats))
            @foreach ($cats as $cat )
            <div class="block_our_works">
                <div class="cat_title_our_works">{{ $cat->title }}</div>
                <?php $our_works=OurWork::orderby('id','desc')->where('category_id',$cat->id)->get()?>
                <div class="ul_our_products">
                    @if (count($our_works)>0)
                        @foreach ($our_works as $our_work )
                            <div class="li_our_work">
                                <div class="title_before_our_works">До уборки</div>
                                <div class="title_after_our_works">После уборки</div>
                                <img src="{{asset('/storage/'.$our_work->img_before )}}" alt="">
                                <img src="{{asset( '/storage/'.$our_work->img_after )}}" alt="">
                            </div>
                        @endforeach
                    @endif
                </div>

            </div>
            @endforeach
        @endif
        
    <div class="text_block_mini">{!! get_acf(19,'content_2') !!}    </div>
    </div>
</section>






@include('forms.search-form') 

@include('footer.footer') 