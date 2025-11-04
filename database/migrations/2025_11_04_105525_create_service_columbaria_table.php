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
        Schema::create('service_columbaria', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title');
            $table->integer('price')->nullable();
            $table->unsignedBigInteger('columbarium_id')->index('service_columbaria_columbarium_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_columbaria');
    }
};
