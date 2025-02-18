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
        Schema::create('image_product_price_lists', function (Blueprint $table) {
            $table->id(); // bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT
            $table->timestamps(); // created_at, updated_at

            $table->unsignedBigInteger('product_price_list_id'); // Внешний ключ
            $table->string('img_before', 255)->collation('utf8mb4_unicode_ci'); // varchar(255)
            $table->string('img_after', 255)->collation('utf8mb4_unicode_ci'); // varchar(255)

            // Внешний ключ с каскадным удалением
            // $table->foreign('product_price_list_id')->references('id')->on('product_price_lists')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_product_price_lists');
    }
};
