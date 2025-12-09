<div
    {{-- Устанавливаем опрос, который прекратится, когда $isFinished станет true --}}
    @if (!$isFinished)
        wire:poll.{{ $this->polling }}ms="updateProgress"
    @endif
    class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow space-y-6 border border-gray-200 dark:border-gray-700"
>

    {{-- Секция 1: Прогресс (Пока не завершено) --}}
    @if (!$isFinished)
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                Статус импорта:
                <span class="font-bold @if($status === 'Failed') text-red-600 @else text-blue-600 dark:text-blue-400 @endif">
                    {{ $status }}
                </span>
            </h3>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ $current }} из {{ $total }} ({{ $percentage }}%)
            </p>
        </div>

        <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700">
            <div
                class="h-3 rounded-full bg-primary-500 dark:bg-primary-500"
                style="width: {{ $percentage }}%"
            ></div>
        </div>

        @if (!empty($errors))
            <div class="p-3 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
                <span class="font-medium">Обнаружены ошибки:</span> {{ count($errors) }} шт. Проверьте лог.
            </div>
        @endif

        {{-- Секция 2: Результаты (Когда завершено) --}}
    @else
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
            Импорт завершен!
        </h3>

        <div class="space-y-2 text-base text-gray-950 dark:text-white">
            <div class="flex items-center">
                Импортировано: {{ $createdCount }} записей
            </div>
            <div class="flex items-center">
                Пропущено: {{ $skippedCount }} строк
            </div>
            @if (count($errors) > 0)
                <div class="flex items-center">
                    Пропущено с ошибками: {{ count($errors) }}шт.
                </div>
            @endif
        </div>

        <div>
            {{-- Кнопка для запуска метода в родительском компоненте Filament Page --}}
            <x-filament::button
                wire:click="$dispatch('refreshPage')"
                color="primary"
                size="md"
            >
                Закрыть
            </x-filament::button>
        </div>
    @endif
</div>
