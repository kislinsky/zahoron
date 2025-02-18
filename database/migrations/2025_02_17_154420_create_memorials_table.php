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
        Schema::create('memorials', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->timestamps(); // created_at & updated_at

            $table->unsignedBigInteger('city_id'); // Внешний ключ city_id
            $table->unsignedBigInteger('district_id'); // Внешний ключ district_id
            $table->date('date'); // Дата бронирования
            $table->text('time'); // Время бронирования
            $table->integer('count'); // Количество участников

            $table->unsignedBigInteger('user_id'); // Внешний ключ user_id
            $table->unsignedBigInteger('organization_id')->nullable(); // Внешний ключ organization_id, может быть NULL

            $table->integer('status')->default(0); // Статус (по умолчанию 0)
            $table->integer('count_time'); // Количество времени
            $table->text('call_time')->nullable(); // Время звонка (может быть NULL)

            // Связь с таблицей cities (каскадное удаление)
            $table
                ->foreign('city_id')
                ->references('id')->on('cities')
                ->cascadeOnDelete();

            // Связь с таблицей districts (каскадное удаление)
            $table
                ->foreign('district_id')
                ->references('id')->on('districts')
                ->cascadeOnDelete();

            // Связь с таблицей users (каскадное удаление)
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
        Schema::dropIfExists('memorials');
    }
};
