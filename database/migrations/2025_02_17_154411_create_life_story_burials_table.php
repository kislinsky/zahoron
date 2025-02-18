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
        Schema::create('life_story_burials', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->timestamps(); // created_at & updated_at (могут быть NULL)

            $table->unsignedBigInteger('burial_id'); // Внешний ключ burial_id
            $table->text('content')->collation('utf8mb4_unicode_ci'); // Содержимое комментария
            $table->unsignedBigInteger('user_id'); // Внешний ключ user_id
            $table->integer('status'); // Статус

            // Связь с таблицей burials (Удаляем комментарии при удалении места захоронения)
            $table
                ->foreign('burial_id')
                ->references('id')->on('burials')
                ->cascadeOnDelete();

            // Связь с таблицей users (Удаляем комментарии при удалении пользователя)
            $table
                ->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('life_story_burials');
    }
};
