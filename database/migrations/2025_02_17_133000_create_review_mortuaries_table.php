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
        Schema::create('review_mortuaries', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('name');
            $table->text('content');
            $table->unsignedBigInteger('mortuary_id');
            $table->integer('status')->default(0);
            $table->integer('rating')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
    
            $table->foreign('mortuary_id')->references('id')->on('mortuaries');
            $table->foreign('city_id')->references('id')->on('cities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_mortuaries');
    }
};
