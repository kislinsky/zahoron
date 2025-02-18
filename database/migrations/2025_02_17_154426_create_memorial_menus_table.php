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
        Schema::create('memorial_menus', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->timestamps(); // created_at & updated_at

            $table->text('title'); // Заголовок
            $table->text('content'); // Содержимое

            $table->unsignedBigInteger('product_id'); // Внешний ключ product_id (связь с products.id)

            // Связь с таблицей products (каскадное удаление)
            $table
                ->foreign('product_id')
                ->references('id')->on('products')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memorial_menus');
    }
};
