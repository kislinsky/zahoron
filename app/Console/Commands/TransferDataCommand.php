<?php
// app/Console/Commands/TransferDataCommand.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TransferDataCommand extends Command
{
    protected $signature = 'data:transfer 
                          {--chunk=1000 : Chunk size for large tables}
                          {--tables=all : Specific tables to transfer}';
    
    protected $description = 'Transfer data from old database to new database';

    public function handle()
    {
        // Сначала запустите миграции если еще не сделали
        $this->call('migrate', ['--force' => true]);

        $tables = $this->getTables();
        
        $this->info("Transferring data for " . count($tables) . " tables...");
        
        foreach ($tables as $table) {
            $this->transferTableData($table);
        }
        
        $this->info('Data transfer completed!');
    }

    protected function getTables()
    {
        if ($this->option('tables') === 'all') {
            return DB::connection('zahoron_old')->getDoctrineSchemaManager()->listTableNames();
        }
        
        return explode(',', $this->option('tables'));
    }

    protected function transferTableData($table)
    {
        $this->info("Transferring: {$table}");
        
        $chunkSize = $this->option('chunk');
        $totalTransferred = 0;
        
        // Отключаем внешние ключи для ускорения
        Schema::disableForeignKeyConstraints();

        DB::connection('zaharon')->table($table)->truncate();

        DB::connection('zahoron_old')
            ->table($table)
            ->orderBy('id')
            ->chunk($chunkSize, function ($records) use ($table, &$totalTransferred) {
                $data = [];
                foreach ($records as $record) {
                    $data[] = (array)$record;
                }
                
                DB::connection('zaharon')->table($table)->insert($data);
                $totalTransferred += count($data);
                
                $this->info("  Chunk transferred: {$totalTransferred} records");
            });

        Schema::enableForeignKeyConstraints();
        
        $this->info("Completed: {$table} ({$totalTransferred} records)");
    }
}