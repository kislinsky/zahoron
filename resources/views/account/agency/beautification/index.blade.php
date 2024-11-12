@include('header.header-organization')
<?php 
    use App\Models\Burial;
    use App\Models\User;
?>
<section class="order_page bac_gray">
    <div class="container">
        <div class="content_order_page">
            <div class="index_title">Облагораживание захоронений</div>    
        </div>
        <img class='rose_order_page'src="{{asset('storage/uploads/rose-with-stem 1 (1).svg')}}" alt="">
        
    </div>
</section>

<section class="orders">
    <div class="container">
        <div class="ul_orders">
            @if(isset($beautifications_burial))
                @if(count($beautifications_burial)>0)
                    @foreach ($beautifications_burial as $beautification_burial)
                        <div class="li_order">
                            <?php if($beautification_burial->product_id!=null){$burial=Burial::findOrFail($beautification_burial->product_id);}?>
                            @if (isset($burial))
                                <div class="title_li decoration_on">{{ $burial->surname }} {{ $burial->name }} {{ $burial->patronymic }}</div>
                                <div class="mini_flex_li_product">
                                    <div class="title_label">Даты захоронения:</div>
                                    <div class="text_li">{{ $burial->date_birth }}-{{ $burial->date_death }}</div>
                                </div> 
                            @endif
                               
                            <div class="mini_flex_li_product">
                                <div class="title_label">Место захоронения:</div>
                                <div class="text_li">{{ $beautification_burial->adres }}</div>
                            </div>
                             @if (isset($burial))
                                <a href='{{ route('burial.single',$burial->id) }}'class="btn_border_blue">Подробнее</a>
                            @endif

                            <div class="mini_flex_li_product">
                                <div class="title_label data_flex">Дата заявки: <p class='text_li'>{{ $beautification_burial->created_at }}</p> </div>
                            </div>
                            <div class="mini_flex_li_product">
                                <div class="title_label data_flex">Размер участка: <p class='text_li'>{{ $beautification_burial->size }}</p> </div>
                            </div>
                            <div class="block_services_order">
                                <div class="ul_services_order services_beautification">
                                    <?php $services=json_decode($beautification_burial->products);?>
                                    @if ($services!=null && count($services)>0)
                                        @foreach ($services as $service)
                                            <div class="title_service_order">— {{ $service }}</div>
                                        @endforeach
                                        
                                    @endif
                                </div>
                            </div>
                            <div class="mini_flex_li_product">
                                <div class="title_label">Имя: {{ User::findOrFail($beautification_burial->user_id)->name}}</div>
                            </div>
                            @if ($beautification_burial->worker_id==null)
                                <a href='{{ route('account.organization.beatification.accept',$beautification_burial->id) }}'class="blue_btn">Принять</a>
                            @endif
                        </div>
                    @endforeach
                @endif
            @endif
        </div>
    </div>
</section>
@include('footer.footer') 