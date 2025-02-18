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
        Schema::create('info_edit_burials', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->timestamps(); // created_at, updated_at

            $table->unsignedBigInteger('burial_id'); // Внешний ключ burial_id
            $table->unsignedBigInteger('user_id'); // Внешний ключ user_id

            $table->string('name', 255)->collation('utf8mb4_unicode_ci'); 
            $table->string('surname', 255)->collation('utf8mb4_unicode_ci'); 
            $table->string('patronymic', 255)->collation('utf8mb4_unicode_ci'); 
            $table->string('date_birth', 255)->collation('utf8mb4_unicode_ci'); 
            $table->string('date_death', 255)->collation('utf8mb4_unicode_ci'); 
            $table->string('who', 255)->collation('utf8mb4_unicode_ci'); 

            $table->integer('status')->default(0); // Статус, по умолчанию 0

            // Связь с таблицей \burials\ (каскадное удаление)
            $table->foreign('burial_id')->references('id')->on('burials')->cascadeOnDelete();

            // Связь с таблицей \users\ (каскадное удаление)
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('info_edit_burials');
    }
};
