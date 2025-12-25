<?php

namespace App\Jobs;

use App\Services\Parser\ParserCemeteryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class CemeteryImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $file;
    protected array $columnMapping;
    protected string $importType;
    protected array $columnsToUpdate;
    protected float $priceGeo;
    protected string $jobId;

    public int $timeout = 3600;

    /**
     * Создание нового Job'а.
     */
    public function __construct(
        string $file,
        array $columnMapping,
        string $importType,
        array $columnsToUpdate,
        float $priceGeo,
        string $jobId
    ) {
        $this->file = $file;
        $this->columnMapping = $columnMapping;
        $this->importType = $importType;
        $this->columnsToUpdate = $columnsToUpdate;
        $this->priceGeo = $priceGeo;
        $this->jobId = $jobId;
    }

    /**
     * Выполнить Job.
     */
    public function handle(ParserCemeteryService $parserService): void
    {
        $parserService->importFromFilament(
            $this->file,
            $this->columnMapping,
            $this->importType,
            $this->columnsToUpdate,
            $this->priceGeo,
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
        Log::error("CemeteryImportJob Failed for {$this->jobId}: " . $exception->getMessage());
    }
}

