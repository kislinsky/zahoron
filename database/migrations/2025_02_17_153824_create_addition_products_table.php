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
        Schema::create('addition_products', function (Blueprint $table) {
            $table->id(); // bigint UNSIGNED AUTO_INCREMENT (Первичный ключ)
            $table->timestamps(); // created_at, updated_at (timestamp NULL)
            
            $table->string('title', 255)->collation('utf8mb4_unicode_ci'); // varchar(255)
            $table->integer('price')->default(0); // int, NOT NULL, default 0
            $table->integer('type'); // int, NOT NULL (добавьте ->nullable() если может быть NULL)

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addition_products');
    }
};
