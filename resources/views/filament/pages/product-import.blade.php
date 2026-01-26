<x-filament::page>
    <div class="space-y-6">

        {{-- 1. Если импорт идет, показываем прогресс-бар Livewire --}}
        @if ($this->isImporting && $this->jobId)
            @livewire(\App\Livewire\ImportProgress::class, ['jobId' => $this->jobId], key('import-progress-' . $this->jobId))
        @endif

        {{-- 2. Если импорт не идет, показываем форму --}}
        @if (!$this->isImporting)
            <form wire:submit="handleImport" class="filament-forms-form">

                {{ $this->form }}

                <x-filament-actions::actions
                    :actions="$this->getFormActions()"
                    class="mt-6"
                />
            </form>
        @endif
    </div>
</x-filament::page>
