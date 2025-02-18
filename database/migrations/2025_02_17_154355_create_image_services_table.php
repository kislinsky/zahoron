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
        Schema::create('image_services', function (Blueprint $table) {
            $table->id(); // bigint UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->timestamps(); // created_at, updated_at

            $table->unsignedBigInteger('service_id'); // Внешний ключ
            $table->string('img_before', 255)->collation('utf8mb4_unicode_ci'); // varchar(255)
            $table->string('img_after', 255)->collation('utf8mb4_unicode_ci'); // varchar(255)

            // Внешний ключ с каскадным удалением
            // $table->foreign('service_id')->references('id')->on('services')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_services');
    }
};
