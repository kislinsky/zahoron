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
        Schema::create('image_personals', function (Blueprint $table) {
            $table->id(); // bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT
            $table->timestamps(); // created_at, updated_at

            $table->string('title', 255)->collation('utf8mb4_unicode_ci'); // varchar(255)
            
            $table->unsignedBigInteger('burial_id'); // ID захоронения
            $table->integer('status')->default(0); // int NOT NULL DEFAULT 0

            // Внешний ключ с каскадным удалением
            // $table->foreign('burial_id')->references('id')->on('burials')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_personals');
    }
};
