<x-filament-widgets::widget>
    <x-filament::section>
        <div class="p-6 bg-white rounded-lg shadow-md">
            <h2 class="text-xl font-semibold">{{ $this->getHeading() }}</h2>
            <!-- Дополнительный контент -->
            <ul class="list-group margin_top_20">
                <li class="list-group-item text_black">{city} - Город.</li>
                <li class="list-group-item text_black">{title} — Название объекта.</li>
                <li class="list-group-item text_black">{Year} — Текущий год.</li>
                <li class="list-group-item text_black">{adres} — Адрес.</li>
                <li class="list-group-item text_black">{time} — Текущее время.</li>
                <li class="list-group-item text_black">{date} — Текущая дата.</li>
                <li class="list-group-item text_black">{count} — Количество фирм/объектов.</li>
                <li class="list-group-item text_black">{price_min} — Минимальная цена услуги.</li>
                <li class="list-group-item text_black">{price_max} — Максимальная цена услуги.</li>
                <li class="list-group-item text_black">{price_avg} — Средняя цена услуги.</li>
                <li class="list-group-item text_black">{category} — Категория.</li>
        
                <li class="list-group-item text_black">{cemetery} — Кладбище захоронения.</li>
                <li class="list-group-item text_black">{name} — Имя.</li>
                <li class="list-group-item text_black">{surname} — Фамилия.</li>
                <li class="list-group-item text_black">{patronymic} — Отчество.</li>
        
            </ul>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
