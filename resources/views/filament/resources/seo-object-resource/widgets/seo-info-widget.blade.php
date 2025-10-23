<x-filament-widgets::widget>
    <x-filament::section>
        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $this->getHeading() }}</h2>
            <!-- Дополнительный контент -->
            <ul class="list-group mt-5">
                <li class="list-group-item text-gray-900 dark:text-gray-100">{city} - Город.</li>
                <li class="list-group-item text-gray-900 dark:text-gray-100">{title} — Название объекта.</li>
                <li class="list-group-item text-gray-900 dark:text-gray-100">{Year} — Текущий год.</li>
                <li class="list-group-item text-gray-900 dark:text-gray-100">{adres} — Адрес.</li>
                <li class="list-group-item text-gray-900 dark:text-gray-100">{time} — Текущее время.</li>
                <li class="list-group-item text-gray-900 dark:text-gray-100">{date} — Текущая дата.</li>
                <li class="list-group-item text-gray-900 dark:text-gray-100">{count} — Количество фирм/объектов.</li>
                <li class="list-group-item text-gray-900 dark:text-gray-100">{price_min} — Минимальная цена услуги.</li>
                <li class="list-group-item text-gray-900 dark:text-gray-100">{price_max} — Максимальная цена услуги.</li>
                <li class="list-group-item text-gray-900 dark:text-gray-100">{price_avg} — Средняя цена услуги.</li>
                <li class="list-group-item text-gray-900 dark:text-gray-100">{category} — Категория.</li>
        
                <li class="list-group-item text-gray-900 dark:text-gray-100">{cemetery} — Кладбище захоронения.</li>
                <li class="list-group-item text-gray-900 dark:text-gray-100">{name} — Имя.</li>
                <li class="list-group-item text-gray-900 dark:text-gray-100">{surname} — Фамилия.</li>
                <li class="list-group-item text-gray-900 dark:text-gray-100">{patronymic} — Отчество.</li>
            </ul>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>