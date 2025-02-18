<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('service_columbaria', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->integer('price')->nullable();
            $table->unsignedBigInteger('columbarium_id');
            $table->timestamps();
    
            $table->foreign('columbarium_id')->references('id')->on('columbaria')->cascadeOnDelete();
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
