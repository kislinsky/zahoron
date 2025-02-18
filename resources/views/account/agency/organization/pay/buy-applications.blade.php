@extends('account.agency.components.page')
@section('title', "Покупка заявок и звонков")

@section('content')

@if($organization!=null)
    <div class="block_buy_aplication">
        
        <div class="text_black">{{get_acf(6,'content')}}</div>

        @foreach ($types_aplications as $type_aplication)
            <div>
                <div class="title_middle">{{$type_aplication->title_ru}}</div>
                <div class="ul_aplications">
                    @foreach ($type_aplication->typeService as $type_service)
                        <div class="li_buy_aplication">
                            <div class="text_middle_index">Заявки на {{ $type_service->title_ru }}</div>
                            <div class="text_black_bold">Осталось:{{ $type_service->count() }}</div>
                            <form action='{{ route('account.agency.applications.pay',$type_service->id) }}' class="flex_input_form_contacts">
                                <div class="block_input">
                                    <input placeholder='10' type="number" min=1 name='count' >
                                </div>
                                <button class="blue_btn">Купить</button>
                            </form >
                        </div>
                    @endforeach
                </div>
            </div>
            
        @endforeach
@else
 <div class="text_black">
    У вас нет привязанных организаций
 </div>
@endif

@endsection