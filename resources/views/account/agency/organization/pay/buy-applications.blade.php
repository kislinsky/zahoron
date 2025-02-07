@extends('account.agency.components.page')
@section('title', "Покупка заявок и звонков")

@section('content')

@if($organization!=null)
    <div class="block_buy_aplication">
        
        <div class="text_black">{{get_acf(6,'content')}}</div>

        <div class="li_buy_aplication">
            <div class="text_middle_index">Заявки на ритуальные услуги</div>
            <div class="text_black_bold">Осталось: {{$organization->applications_funeral_services}}</div>
            <form action={{route('account.agency.applications.funeral-services.buy')}} class="flex_input_form_contacts">
                <div class="block_input">
                    <input placeholder='10' type="number" min=1 name='applications_funeral_services' >
                </div>
                <button class="blue_btn">Купить</button>
            </form >
        </div>

        <div class="li_buy_aplication">
            <div class="text_middle_index">Звонки в организацию</div>
            <div class="text_black_bold">Осталось: {{$organization->calls_organization}}</div>
            <form action={{route('account.agency.applications.calls-organization.buy')}} class="flex_input_form_contacts">
                <div class="block_input">
                    <input placeholder='10' type="number" min=1 name='calls_organization' >
                </div>
                <button class="blue_btn">Купить</button>
            </form >
        </div>

        <div class="li_buy_aplication">
            <div class="text_middle_index">Заявки товаров с маркетплейса</div>
            <div class="text_black_bold">Осталось: {{$organization->product_requests_from_marketplace}}</div>
            <form action={{route('account.agency.applications.product-marketplace.buy')}} class="flex_input_form_contacts">
                <div class="block_input">
                    <input placeholder='10' type="number" min=1 name='product_requests_from_marketplace' >
                </div>
                <button class="blue_btn">Купить</button>
            </form >
        </div>

        <div class="li_buy_aplication">
            <div class="text_middle_index">Заявки на облагораживание могил</div>
            <div class="text_black_bold">Осталось: {{$organization->applications_improvemen_graves}}</div>
            <form action={{route('account.agency.applications.improvemen-graves.buy')}} class="flex_input_form_contacts">
                <div class="block_input">
                    <input placeholder='10' type="number" min=1 name='applications_improvemen_graves' >
                </div>
                <button class="blue_btn">Купить</button>
            </form >
        </div>

        <div class="li_buy_aplication">
            <div class="text_middle_index">Заявки на поминки</div>
            <div class="text_black_bold">Осталось: {{$organization->aplications_memorial}}</div>
            <form action={{route('account.agency.applications.memorial.buy')}} class="flex_input_form_contacts">
                <div class="block_input">
                    <input placeholder='10' type="number" min=1 name='aplications_memorial' >
                </div>
                <button class="blue_btn">Купить</button>
            </form >
        </div>
        
    </div>
@else
 <div class="text_black">
    У вас нет привязанных организаций
 </div>
@endif

@endsection