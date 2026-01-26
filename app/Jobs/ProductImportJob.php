<?php

namespace App\Jobs;

use App\Services\Parser\ParserProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class ProductImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $file;
    protected array $columnMapping;

    protected string $jobId;

    public int $timeout = 3600;

    /**
     * Создание нового Job'а.
     */
    public function __construct(
        string $file,
        array $columnMapping,
        string $jobId
    ) {
        $this->file = $file;
        $this->columnMapping = $columnMapping;
        $this->jobId = $jobId;
    }

    /**
     * Выполнить Job.
     */
    public function handle(ParserProduct $parserService): void
    {
        $disk = Storage::disk('public');
        $originalFile = $this->file;
        $absolutePath = $disk->path($originalFile);

        $extension = pathinfo($absolutePath, PATHINFO_EXTENSION);

        // Если файл XLSX, конвертируем его в CSV перед импортом
        if (strtolower($extension) === 'xlsx') {
            $csvFile = preg_replace('/\.xlsx$/i', '.csv', $originalFile);
            $absoluteCsvPath = $disk->path($csvFile);

            $command = "xlsx2csv " . escapeshellarg($absolutePath) . " " . escapeshellarg($absoluteCsvPath);
            shell_exec($command);

            // Если конвертация прошла успешно, работаем с новым файлом
            if (file_exists($absoluteCsvPath)) {
                // Удаляем старый XLSX сразу, чтобы не плодить файлы
                $disk->delete($originalFile);
                $this->file = $csvFile;
            }
        }

        $parserService->importFromFilament(
            $this->file,
            $this->columnMapping,
            $this->jobId
        );

        Storage::disk('public')->delete($this->file);
    }

    /**
     * Обработка сбоя Job'а
     */
    public function failed(\Throwable $exception): void
    {
        Storage::disk('public')->delete($this->file);

        Redis::set("import_progress:{$this->jobId}:status", 'Failed');
        Redis::set("import_progress:{$this->jobId}:errors", json_encode(['critical' => $exception->getMessage()]));
        Log::error("Job Failed for {$this->jobId}: " . $exception->getMessage());
    }
}
