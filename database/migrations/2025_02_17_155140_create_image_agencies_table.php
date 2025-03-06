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
        Schema::create('image_agencies', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
            $table->timestamps(); // created_at & updated_at

            $table->string('title', 255); // Название

            // Внешний ключ (user_id) с каскадным удалением
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete(); // Удаляет связанные записи при удалении пользователя
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_agencies');
    }
};
