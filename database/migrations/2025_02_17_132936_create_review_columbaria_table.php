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
    Schema::create('review_columbaria', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
        $table->text('name');
        $table->text('content');
        $table->integer('rating')->nullable();
        $table->unsignedBigInteger('columbarium_id');
        $table->integer('status')->default(0);
        $table->unsignedBigInteger('city_id')->nullable();

        $table->foreign('columbarium_id')->references('id')->on('columbaria')->cascadeOnDelete();
        $table->foreign('city_id')->references('id')->on('cities')->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_columbaria');
    }
};
