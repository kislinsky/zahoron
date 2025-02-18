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
        Schema::create('favourite_burials', function (Blueprint $table) {
            $table->id(); // bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT
            $table->timestamps(); // created_at, updated_at

            $table->unsignedBigInteger('burial_id'); // ID могилы
            $table->unsignedBigInteger('user_id'); // ID пользователя

            // Внешние ключи с каскадным удалением
            $table->foreign('burial_id')->references('id')->on('burials')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favourite_burials');
    }
};
