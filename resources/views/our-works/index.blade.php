@include('header.header')
<?php 

use App\Models\OurWork;
$count_projects=count(OurWork::orderby('id','desc')->get());
?>
<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <h1 class="index_title">Наши работы</h1>    
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
                <div class="text_block_mini">Сотни убранных могил среди наших отчетов оправдывают ваше доверие,вы можете посмотреть результаты наших работ ниже</div>
            </div>
            <div class="block_one_our_works">
                <video controls src="{{ asset('storage/uploads/Главная - Opera 2024-07-24 17-20-05.mp4') }}"></video>
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
                                <img src="{{asset('storage/uploads_our_works/'. $our_work->img_before )}}" alt="">
                                <img src="{{asset('storage/uploads_our_works/'. $our_work->img_after )}}" alt="">
                            </div>
                        @endforeach
                    @endif
                </div>

            </div>
            @endforeach
        @endif
        
    <div class="text_block_mini">
        Работы могут проводиться в разное время года и в любое удобное для Вас время. Вы можете заказать уборку могилы как разовую, например, к определенной дате (годовщине смерти или поминальному дню), так и комплексную – на месяц или на год.<br><br>Наши рабочие всегда компетентно и ответственно выполнят всю работу и в случае необходимости сделают фотографии "до" и "после" уборки, чтобы Вы точно убедились в их работе.<br><br>Стоимость услуг зависит от объема работ, срочности заказа и требуемых результатов, поэтому все вопросы по поводу цены Вы можете узнать по контактному телефону.<br><br> <strong>ВАЖНО ПОМНИТЬ!</strong><br>Содержание могилы и захоронения в надлежащем виде является Вашей обязанностью! При отсутствии надлежащего ухода за местами захоронения они признаются бесхозными. Поэтому всегда следует помнить об их уборке и приводить их в надлежащий вид если не собственными силами, то силами компаний, оказывающих услуги по уборке могилы.
    </div>
    </div>
</section>






@include('forms.search-form') 

@include('footer.footer') 