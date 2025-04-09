<x-filament::page>
    <form wire:submit.prevent="submit">
        {{ $this->form }}<br>

        <x-filament::button type="submit">
            Загрузить и отправить
        </x-filament::button>
    </form>
</x-filament::page>