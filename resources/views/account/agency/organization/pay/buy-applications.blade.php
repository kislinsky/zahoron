@extends('account.agency.components.page')
@section('title', "Покупка заявок и звонков")

@section('content')

@if($organization!=null)
    <div class="block_buy_aplication">
        <div class="text_black">{{get_acf(6,'content')}}</div>

        @foreach ($types_aplications as $type_aplication)
            <div class="service-category">
                <div class="title_middle">{{$type_aplication->title_ru}}</div>
                <div class="services-table">
                    <table class="pricing-table">
                        <thead>
                            <tr>
                                <th>Название услуги</th>
                                <th>Стандартная цена</th>
                                <th>Цена с премиумом</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($type_aplication->typeService as $type_service)
                                @if($type_service->is_show==1)
                                    <tr>
                                        <td class="service-name">{{ $type_service->title_ru }}</td>
                                        <td class="standard-price">{{ $type_service->price ?? '0' }} руб.</td>
                                        <td class="premium-price">{{ $type_service->premium_price ?? '0' }} руб.</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text_black">
        У вас нет привязанных организаций
    </div>
@endif

@endsection