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
    Schema::create('crematoria', function (Blueprint $table) {
        $table->id();
        $table->text('title');
        $table->unsignedBigInteger('city_id');
        $table->string('width', 250);
        $table->string('longitude', 250);
        $table->text('content');
        $table->text('mini_content');
        $table->text('img_url')->nullable();
        $table->text('img_file')->nullable();
        $table->text('adres');
        $table->integer('rating')->nullable();
        $table->string('time_start_work', 250)->nullable();
        $table->string('time_end_work', 250)->nullable();
        $table->text('next_to')->nullable();
        $table->text('underground')->nullable();
        $table->text('characteristics')->nullable();
        $table->string('phone', 250)->nullable();
        $table->integer('href_img')->default(0);
        $table->string('village', 255)->nullable();
        $table->text('email')->nullable();
        $table->integer('time_difference')->default(0);
        $table->timestamps();

        // $table->foreign('city_id')->references('id')->on('cities')->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crematoria');
    }
};
