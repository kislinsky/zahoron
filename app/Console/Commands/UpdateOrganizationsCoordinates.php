<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Services\DadataService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateOrganizationsCoordinates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'organizations:update-coordinates 
                            {--chunk=100 : Количество организаций для обработки за один раз} 
                            {--dry-run : Режим тестирования без сохранения изменений}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление координат организаций по их адресам с помощью DaData API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $chunkSize = (int)$this->option('chunk');

        if ($dryRun) {
            $this->info('Запуск в тестовом режиме (изменения не будут сохранены)');
        }

        $total = Organization::whereNotNull('adres')->count();
        $this->info("Найдено организаций для обработки: {$total}");
        
        $progressBar = $this->output->createProgressBar($total);
        $progressBar->start();

        $updated = 0;
        $failed = 0;

        Organization::whereNotNull('adres')
            ->with('city') // Жадная загрузка города
            ->chunkById($chunkSize, function ($organizations) use ($progressBar, &$updated, &$failed, $dryRun) {
                foreach ($organizations as $organization) {
                    try {
                        $dadata = new DadataService();
                        $address = $organization->city->title . ',' . $organization->adres;
                        
                        $coordinates = $dadata->getCoordinatesByAddress($address);

                        if ($coordinates && isset($coordinates['lat']) && isset($coordinates['lon'])) {
                            if (!$dryRun) {
                                $organization->update([
                                    'width' => $coordinates['lat'],
                                    'longitude' => $coordinates['lon'],
                                ]);
                            }
                            $updated++;
                        } else {
                            $failed++;
                            Log::warning("Не удалось получить координаты для организации ID: {$organization->id}, адрес: {$address}");
                        }
                    } catch (\Exception $e) {
                        $failed++;
                        Log::error("Ошибка при обработке организации ID: {$organization->id}: " . $e->getMessage());
                    }

                    $progressBar->advance();
                }
            });

        $progressBar->finish();

        $this->newLine(2);
        $this->info("Обработка завершена!");
        $this->info("Успешно обновлено: {$updated}");
        $this->info("Не удалось обновить: {$failed}");
        
        if ($dryRun) {
            $this->warn('Внимание: команда запущена в тестовом режиме, изменения не сохранены!');
        }

        return Command::SUCCESS;
    }
}