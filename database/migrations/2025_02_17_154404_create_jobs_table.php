<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY

            $table->string('queue', 255)->collation('utf8mb4_unicode_ci'); // Индексируемое поле
            $table->longText('payload')->collation('utf8mb4_unicode_ci'); // Данные
            $table->tinyInteger('attempts')->unsigned(); // Попытки выполнения
            $table->unsignedInteger('reserved_at')->nullable(); // Время резервирования
            $table->unsignedInteger('available_at'); // Доступное время
            $table->unsignedInteger('created_at'); // Время создания
            
            // Индекс для поля queue
            $table->index('queue'); 
 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
