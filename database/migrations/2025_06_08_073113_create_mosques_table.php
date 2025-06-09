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
        Schema::create('mosques', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->unsignedBigInteger('city_id');
            $table->text('latitude')->nullable();
            $table->text('longitude')->nullable();
            $table->text('content');
            $table->text('mini_content')->nullable();
            $table->string('img_url', 255)->nullable();
            $table->text('img_file')->nullable();
            $table->text('address');
            $table->integer('rating')->nullable();
            $table->text('next_to')->nullable();
            $table->text('underground')->nullable();
            $table->text('characteristics')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->string('phone', 250)->nullable();
            $table->integer('href_img')->default(0);
            $table->string('village', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->integer('time_difference')->default(0);
            $table->timestamps();

            // Внешний ключ для связи с таблицей cities
            $table->foreign('city_id')->references('id')->on('cities')->cascadeOnDelete();
        });

        // Создаем таблицу для изображений мечетей
        Schema::create('image_mosques', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mosque_id');
            $table->string('img_file')->nullable();
            $table->string('img_url')->nullable();
            $table->integer('href_img')->default(1);
            $table->timestamps();

            // Внешний ключ для связи с таблицей mosques
            $table->foreign('mosque_id')->references('id')->on('mosques')->cascadeOnDelete();
        });

        // Таблица отзывов для церквей
        Schema::create('review_mosques', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('name');
            $table->text('content');
            $table->unsignedBigInteger('mosque_id');
            $table->integer('status')->default(0);
            $table->integer('rating')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            
            $table->foreign('mosque_id')->references('id')->on('mosques')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
        });

        Schema::create('working_hours_mosques', function (Blueprint $table) {
            $table->id();
            $table->string('time_start_work', 255)->nullable();
            $table->string('time_end_work', 255)->nullable();
            $table->integer('holiday')->default(0);
            $table->unsignedBigInteger('mosque_id');
            $table->string('day', 255);
            $table->timestamps();

            $table->foreign('mosque_id')->references('id')->on('mosques')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удаляем таблицы в обратном порядке создания
        Schema::dropIfExists('image_mosques');
        Schema::dropIfExists('review_mosques');
        Schema::dropIfExists('working_hours_mosques');
        Schema::dropIfExists('mosques');
    }
};