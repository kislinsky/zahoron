<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ticket_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->default('#3498db');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Добавляем начальные данные
        DB::table('ticket_categories')->insert([
            ['name' => 'Техническая проблема', 'color' => '#e74c3c', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Вопрос по оплате', 'color' => '#27ae60', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Общий вопрос', 'color' => '#3498db', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Предложение', 'color' => '#f39c12', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('ticket_categories');
    }
};