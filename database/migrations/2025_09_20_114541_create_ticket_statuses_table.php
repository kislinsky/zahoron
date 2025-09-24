<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ticket_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->default('#3498db');
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
        });

        // Добавляем начальные данные
        DB::table('ticket_statuses')->insert([
            ['name' => 'Открыт', 'color' => '#3498db', 'is_closed' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'В работе', 'color' => '#f39c12', 'is_closed' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Решен', 'color' => '#27ae60', 'is_closed' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Закрыт', 'color' => '#95a5a6', 'is_closed' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('ticket_statuses');
    }
};