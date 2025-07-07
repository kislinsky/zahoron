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
        Schema::create('message_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Название шаблона');
            $table->string('slug')->unique()->comment('Уникальный идентификатор');
            $table->enum('type', ['email', 'sms'])->default('email')->comment('Тип шаблона');
            $table->string('subject')->nullable()->comment('Тема письма (для email)');
            $table->text('template')->comment('Шаблон сообщения');
            $table->json('variables')->nullable()->comment('Доступные переменные для подстановки');
            $table->string('description')->nullable()->comment('Описание шаблона');
            $table->boolean('is_active')->default(true)->comment('Активен ли шаблон');
            $table->timestamps();
            
            $table->index('slug');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_templates');
    }
};