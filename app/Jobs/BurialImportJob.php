<?php

namespace App\Jobs;

use App\Services\Parser\ParserBurialService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class BurialImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $file;
    protected array $columnMapping;
    protected ?int $defaultCemeteryId;
    protected string $jobId;

    public int $timeout = 3600;

    /**
     * Создание нового Job'а.
     */
    public function __construct(
        string $file,
        array $columnMapping,
        ?int $defaultCemeteryId,
        string $jobId
    ) {
        $this->file = $file;
        $this->columnMapping = $columnMapping;
        $this->defaultCemeteryId = $defaultCemeteryId;
        $this->jobId = $jobId;
    }

    /**
     * Выполнить Job.
     */
    public function handle(ParserBurialService $parserService): void
    {
        $parserService->importFromFilament(
            $this->file,
            $this->columnMapping,
            $this->defaultCemeteryId,
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
