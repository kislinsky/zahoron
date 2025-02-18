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
    Schema::create('mortuaries', function (Blueprint $table) {
        $table->id();
        $table->text('title');
        $table->unsignedBigInteger('city_id');
        $table->text('width')->nullable();
        $table->text('longitude')->nullable();
        $table->text('content');
        $table->text('mini_content')->nullable();
        $table->string('img_url', 255)->nullable();
        $table->text('img_file')->nullable();
        $table->text('adres');
        $table->integer('rating')->nullable();
        $table->text('next_to')->nullable();
        $table->text('underground')->nullable();
        $table->text('characteristics')->nullable();
        $table->unsignedBigInteger('district_id')->nullable();
        $table->string('phone', 250)->nullable();
        $table->integer('href_img')->default(0);
        $table->string('village', 255)->nullable();
        $table->string('email', 255)->nullable();
        $table->integer('time_difference')->default(0);
        $table->timestamps();

        // $table->foreign('city_id')->references('id')->on('cities')->cascadeOnDelete();
        // $table->foreign('district_id')->references('id')->on('districts')->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mortuaries');
    }
};
