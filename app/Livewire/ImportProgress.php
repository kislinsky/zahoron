<?php

namespace App\Livewire;

use App\Filament\Pages\BurialImport;
use Illuminate\Support\Facades\Redis;
use Livewire\Component;

class ImportProgress extends Component
{
    public string $jobId;
    public int $total = 0;
    public int $current = 0;
    public int $percentage = 0;
    public string $status = 'Ожидание';
    public array $errors = [];
    public bool $isFinished = false;
    public bool $eventDispatched = false;
    public int $createdCount = 0;
    public int $skippedCount = 0;
    protected $polling = 1000;

    public function mount(string $jobId): void
    {
        $this->jobId = $jobId;
        $this->updateProgress();
    }

    /**
     * Вызывается при опросе Livewire (polling)
     */
    public function updateProgress(): void
    {
        // 1. Чтение данных из Redis
        $total = (int) Redis::get("import_progress:{$this->jobId}:total");
        $current = (int) Redis::get("import_progress:{$this->jobId}:current");
        $status = Redis::get("import_progress:{$this->jobId}:status") ?? 'В процессе';
        $errorsJson = Redis::get("import_progress:{$this->jobId}:errors");

        // 2. Обновление свойств компонента
        $this->total = $total;
        $this->current = $current;
        $this->status = $status;
        $this->errors = $errorsJson ? json_decode($errorsJson, true) : [];

        // 3. Расчет процента
        if ($this->total > 0) {
            $this->percentage = (int) round(($this->current / $this->total) * 100);
        } else {
            $this->percentage = 0;
        }

        // 4. Логика завершения
        if (in_array($this->status, ['Выполнен', 'Ошибка'])) {
            $this->isFinished = true;

            // Получаем финальные цифры
            $this->createdCount = (int) Redis::get("import_progress:{$this->jobId}:created") ?? 0;
            $this->skippedCount = (int) Redis::get("import_progress:{$this->jobId}:skipped") ?? 0;

            // Отправляем событие в родительский компонент Filament Page для уведомления
            if (!$this->eventDispatched) {
                $this->dispatch('importFinished',
                    status: $this->status,
                    created: $this->createdCount,
                    skipped: $this->skippedCount,
                    errors: $this->errors
                )->to(BurialImport::class);

                $this->eventDispatched = true;
            }

            // Очищаем ключи Redis через 5 минут
            Redis::expire("import_progress:{$this->jobId}:total", 300);
            Redis::expire("import_progress:{$this->jobId}:current", 300);
            Redis::expire("import_progress:{$this->jobId}:status", 300);
            Redis::expire("import_progress:{$this->jobId}:created", 300);
            Redis::expire("import_progress:{$this->jobId}:skipped", 300);
            Redis::expire("import_progress:{$this->jobId}:errors", 300);
        }
    }

    public function render()
    {
        return view('livewire.import-progress');
    }
}
