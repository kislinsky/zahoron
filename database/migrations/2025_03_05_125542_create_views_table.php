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
        Schema::create('views', function (Blueprint $table) {
            $table->id(); // id (Primary Key)
            $table->timestamps(); // created_at & updated_at
            $table->string('entity_type'); // Тип сущности
            $table->unsignedBigInteger('entity_id'); // Идентификатор сущности
            $table->unsignedBigInteger('user_id')->nullable(); // ID пользователя (опционально)
            $table->string('session_id')->nullable(); // Идентификатор сессии (опционально)
            $table->timestamp('timestamp')->useCurrent(); // Дата и время просмотра
            $table->string('source')->nullable(); // Источник просмотра (опционально)
            $table->string('ip_address')->nullable(); // IP-адрес (опционально)
            $table->string('device')->nullable(); // Тип устройства (опционально)
            $table->string('location')->nullable(); // Город или регион (опционально)

            // Индексы для улучшения производительности (опционально)
            $table->index(['entity_type', 'entity_id']);
            $table->index('user_id');
            $table->index('session_id');
            $table->index('timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('views');
    }
};
