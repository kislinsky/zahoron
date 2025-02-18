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
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT
            $table->timestamps(); // created_at & updated_at

            $table->integer('code'); // Код пользователя
            $table->text('phone'); // Телефон
            $table->text('token'); // Токен
            $table->text('role'); // Роль пользователя
            
            $table->string('inn', 255)->nullable(); // ИНН (может быть NULL)
            $table->string('okved', 255)->nullable(); // ОКВЭД (может быть NULL)
            $table->string('contragent', 255)->nullable(); // Контрагент (может быть NULL)
            $table->string('organization_form', 255)->nullable(); // Организационная форма
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
