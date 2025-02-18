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
        Schema::create('faq_cemeteries', function (Blueprint $table) {
            $table->id(); // bigInt UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->timestamps(); // created_at, updated_at

            $table->text('title')->collation('utf8mb4_unicode_ci'); // Заголовок вопроса
            $table->text('content')->collation('utf8mb4_unicode_ci'); // Содержание ответа
            $table->foreignId('cemetery_id')
            ->constrained('cemeteries') // Связываем с таблицей \product_price_lists\
            ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faq_cemeteries');
    }
};
