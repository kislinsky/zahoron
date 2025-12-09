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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            
            // Пользователь, которому предназначено уведомление
            // Если null - уведомление для всей организации
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade');
            
            // Организация
            $table->foreignId('organization_id')
                ->constrained('organizations')
                ->onDelete('cascade');
            
            // Тип уведомления
            $table->string('type'); 
            // order_product, order_burial, beautification, 
            // call, funeral_service, review, comment
            
            // Заголовок и текст уведомления
            $table->string('title');
            $table->text('message');
            
            // Прочитано ли уведомление
            $table->boolean('is_read')->default(false);
            
            // Ссылка на связанный объект (опционально)
            $table->string('link')->nullable();
            
            // Дополнительные данные в JSON (опционально)
            $table->json('data')->nullable();
            
            $table->timestamps();
            
            // Индексы для быстрого поиска
            $table->index(['user_id', 'is_read']);
            $table->index(['organization_id', 'is_read']);
            $table->index(['user_id', 'organization_id', 'type']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};