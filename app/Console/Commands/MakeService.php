<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeService extends Command
{
    /**
     * Название и сигнатура команды.
     *
     * @var string
     */
    protected $signature = 'make:service {name : The name of the service}';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Create a new service class';

    /**
     * Экземпляр файловой системы.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * Создает экземпляр команды.
     *
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Выполнить команду.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $servicePath = app_path("Services/{$name}.php");

        // Проверяем, существует ли файл
        if ($this->files->exists($servicePath)) {
            $this->error("Service {$name} already exists!");
            return 1;
        }

        // Создаем папку Services, если она не существует
        if (!$this->files->isDirectory(app_path('Services'))) {
            $this->files->makeDirectory(app_path('Services'), 0755, true);
        }

        // Генерируем содержимое класса
        $stub = $this->getStub($name);

        // Создаем файл сервиса
        $this->files->put($servicePath, $stub);

        $this->info("Service {$name} created successfully.");
        return 0;
    }

    /**
     * Получить шаблон класса сервиса.
     *
     * @param string $name
     * @return string
     */
    protected function getStub(string $name)
    {
        return <<<PHP
<?php

namespace App\Services;

class {$name}
{
    //
}
PHP;
    }
}
