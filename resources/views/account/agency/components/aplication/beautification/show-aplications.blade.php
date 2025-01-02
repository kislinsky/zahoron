<div class="grid_two grid_mobile_1 margin_top_20">

    @foreach($aplications as $aplication)

        <div class="li_aplication">

            <div class="aplication_flex border__bottom_aplication">
                <div class="title_aplication">Заявка: {{$aplication->created_at->format('d.m.Y')}}</div>
                @if($aplication->status==0)
                    <div class="green_btn">
                        Заявка актуальна до {{$aplication->timeEnd()}}
                    </div>
                @endif
            </div>

            <div class="mini_flex_li_product">
                <div class="title_label">Город:</div>
                <div class="text_li">{{ $aplication->city->title }}</div>
            </div>

            <div class="mini_flex_li_product">
                <div class="title_label">Кладбище:</div>
                <div class="text_li">{{ $aplication->cemetery->title }}</div>
                
            </div>

            <div class="mini_flex_li_product">
                <div class="title_label">Товары:</div>
                @foreach($aplication->categoryPriceList() as $category)
                    <div class="text_li">{{ $category->title }}</div>
                @endforeach
            </div>

            @if($aplication->status==0)
                <form class='accept_order' action="{{route('account.agency.organization.aplication.beautification.accept',$aplication)}}" method="post">
                    @csrf
                    @method('PATCH')
                    <button class='blue_btn'>Принять</button>
                </form>
            @elseif($aplication->status==1)
                <a class='blue_btn' href="tel:{{$aplication->user->phone}}">Позвонить</a>
                <form class='accept_order' action="{{route('account.agency.organization.aplication.beautification.complete',$aplication)}}" method="post">
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

{{ $aplications->withPath(route('account.agency.organization.aplication.beautification.new'))->appends($_GET)->links() }}

