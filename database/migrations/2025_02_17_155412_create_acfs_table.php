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
        Schema::create('acfs', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT
            $table->timestamps();

            $table->string('name', 255);
            $table->text('content');
            $table->string('type', 255)->nullable();

            // Исправляемый внешний ключ
            $table->unsignedBigInteger('page_id'); // Должен быть UNSIGNED BIGINT
            $table->foreign('page_id')
                  ->references('id')->on('pages')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acfs');
    }
};
