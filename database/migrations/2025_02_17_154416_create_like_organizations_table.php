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
        Schema::create('like_organizations', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->timestamps(); // created_at & updated_at (могут быть NULL)

            $table->unsignedBigInteger('user_id'); // Внешний ключ user_id
            $table->unsignedBigInteger('organization_id'); // Внешний ключ organization_id

            // Связь с таблицей users (Удаляем связь при удалении пользователя)
            $table
                ->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();

            // Связь с таблицей organizations (Удаляем связь при удалении организации)
            $table
                ->foreign('organization_id')
                ->references('id')->on('organizations')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('like_organizations');
    }
};
