@extends('account.agency.components.page')
@section('title', "Приоритетное размещение")

@section('content')

    <form action="{{ route('account.agency.priority.pay') }}" method="post">
        @csrf
        <div class="block_input margin_top_20">
            <div class="title_middle">Выберите категорию</div>
            <div class="select">
                <select name="type_priority" >
                    <option value="1">Фирма в списке</option>
                </select>
            </div>
        </div>

        <div class="block_input margin_top_20">
            <div class="title_middle">Выберите приоритет</div>
            <div class="select">
                <select name="priority" >
                    <option value="priority-list-companies-1-3">Приориет 1-3</option>
                    <option value="priority-list-companies-4-6">Приориет 4-6</option>
                </select>
            </div>
        </div>

        <div class="block_input margin_top_20">
            <div class="text_black_bold">Приоритетное размещение в списках включает:</div>
            <div class="text_black_bold margin_top_20">
                -Размещение в списке фирм в соотвествующих категориях.<br>
                -Включение в пятерку лучших фирм в таблицах.
            </div>
            <div class="text_black_bold margin_top_20">Такой подход позволяет повысить видимость компании и привлечь больше клиентов. </div>
        </div>

        <div class="block_input margin_top_20">
            <div class="text_black_bold">Приориет 1-3: {{ getTypeService('priority-list-companies-1-3')->price; }} руб.</div>
            <div class="text_black_bold">Приориет 4-6: {{ getTypeService('priority-list-companies-4-6')->price; }} руб.</div>
        </div>
        <button class='blue_btn margin_top_20'>Оплатить</button>
    </form>

@endsection