@if($aplications->count()>0)
    <div class="grid_two grid_mobile_1 margin_top_20">

        @foreach($aplications as $aplication)

            <div class="li_aplication">

                <div class="aplication_flex border__bottom_aplication">
                    <div class="title_aplication">
                        <?php 
                            if($aplication->service==1){
                                echo 'Отправка груз 200';
                            }
                            elseif($aplication->service==2){
                                echo 'Организация кремации';
                            }
                            elseif($aplication->service==3){
                                echo 'Организация похорон';
                            }
                        ?>
                    </div>
                    @if($aplication->status==0)
                        <div class="green_btn">
                            Заявка актуальна до {{$aplication->timeEnd()}}
                        </div>
                    @endif
                </div>

                <div class="mini_flex_li_product">
                    <div class="title_label">Город @if($aplication->service==1)отправки@endif:</div>
                    <div class="text_li">{{ $aplication->city->title }}</div>
                </div>


                @if($aplication->service==1)
                    <div class="title_label">Город получения:</div>

                    @if($aplication->cityTo()!=null)
                        <div class="text_li">{{ $aplication->cityTo()->title }}</div>
                    @else
                        <div class="text_li">Заграницу</div>
                    @endif

                @endif

                <div class="mini_flex_li_product">
                    <div class="title_label">Морг:</div>
                    @if($aplication->mortuary_id!=null)
                        <div class="text_li">{{ $aplication->mortuary->title }}</div>
                    @else
                        <div class="text_li">Неизвестно</div>
                    @endif
                </div>


                @if($aplication->service==3)
                    <div class="title_label">Кладбище:</div>
                    <div class="text_li">{{ $aplication->cemetery->title }}</div>
                @endif

                <div class="mini_flex_li_product">
                    <div class="title_label">Статус умершего:</div>
                    <div class="text_li">{{ $aplication->status_death }}</div>
                </div>

                <div class="mini_flex_li_product">
                    <div class="title_label">Гражданский статус:</div>
                    <div class="text_li">{{ $aplication->civilian_status_death }}</div>
                </div>

            
                <div class="mini_flex_li_product">
                    <div class="title_label">Отпевание в церкви:</div>
                    @if($aplication->funeral_service_church==1)
                        <div class="text_li">Да</div>
                    @else
                        <div class="text_li">Нет</div>
                    @endif
                </div>

                <div class="mini_flex_li_product">
                    <div class="title_label">Прощальный зал:</div>
                    @if($aplication->farewell_hall==1)
                        <div class="text_li">Да</div>
                    @else
                        <div class="text_li">Нет</div>
                    @endif
                </div>
            
                
                @if($aplication->status==0)
                    <form class='accept_order' action="{{route('account.agency.organization.aplication.funeral-service.accept',$aplication)}}" method="post">
                        @csrf
                        @method('PATCH')
                        <button class='blue_btn'>Принять</button>
                    </form>
                @elseif($aplication->status==1)
                    <a class='blue_btn' href="tel:{{$aplication->user->phone}}">Позвонить</a>
                    <form class='accept_order' action="{{route('account.agency.organization.aplication.funeral-service.complete',$aplication)}}" method="post">
                        @csrf
                        @method('PATCH')
                        <button class='green_btn'>Завершить</button>
                    </form>
                
                @elseif($aplication->status==4)
                    <div class='blue_btn'>Не принята</div>
                @endif
                
            </div>

        @endforeach

    </div>

    {{ $aplications->withPath(route('account.agency.organization.aplication.funeral-service.new'))->appends($_GET)->links() }}
@else
    <div class="text_black">Нет заявок</div>
@endif