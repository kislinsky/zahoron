<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ticket_priorities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->default('#95a5a6');
            $table->integer('level')->default(1);
            $table->timestamps();
        });

        // Добавляем начальные данные
        DB::table('ticket_priorities')->insert([
            ['name' => 'Низкий', 'color' => '#27ae60', 'level' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Средний', 'color' => '#f39c12', 'level' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Высокий', 'color' => '#e74c3c', 'level' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('ticket_priorities');
    }
};