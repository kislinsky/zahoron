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
        Schema::create('advice_product_price_lists', function (Blueprint $table) {
            $table->id(); // bigint UNSIGNED AUTO_INCREMENT (Первичный ключ)
            $table->timestamps(); // created_at, updated_at (timestamp NULL)

            $table->text('title')->collation('utf8mb4_unicode_ci'); // text
            $table->string('img', 255)->collation('utf8mb4_unicode_ci'); // varchar(255)

            // Внешний ключ с каскадным удалением, связанный с \product_price_lists\
            $table->foreignId('product_price_list_id')
                ->constrained('product_price_lists') // Связываем с таблицей \product_price_lists\
                ->cascadeOnDelete(); // Если запись в \product_price_lists\ удалена, то \products\ тоже удалятся
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advice_product_price_lists');
    }
};
