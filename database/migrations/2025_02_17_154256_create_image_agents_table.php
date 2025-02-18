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
        Schema::create('image_agents', function (Blueprint $table) {
            $table->id(); // bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT
            $table->timestamps(); // created_at, updated_at

            $table->string('title', 255)->collation('utf8mb4_unicode_ci'); // varchar(255) utf8mb4_unicode_ci
            
            $table->unsignedBigInteger('user_id'); // ID пользователя

            // Внешний ключ с каскадным удалением
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_agents');
    }
};
