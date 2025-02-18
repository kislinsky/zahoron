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
        Schema::create('image_cat_price_lists', function (Blueprint $table) {
            $table->id(); // bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT
            $table->timestamps(); // created_at, updated_at

            $table->string('img_before', 255)->collation('utf8mb4_unicode_ci'); // varchar(255)
            $table->string('img_after', 255)->collation('utf8mb4_unicode_ci'); // varchar(255)

            $table->unsignedBigInteger('category_id'); // ID категории

            // Внешний ключ с каскадным удалением
            // $table->foreign('category_id')->references('id')->on('category_product_price_lists')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_cat_price_lists');
    }
};
