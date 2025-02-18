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
        Schema::create('columbaria', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->unsignedBigInteger('city_id');
            $table->string('width', 255);
            $table->string('longitude', 255);
            $table->text('content');
            $table->text('mini_content');
            $table->string('img_url', 255)->nullable();
            $table->text('img_file')->nullable();
            $table->text('adres');
            $table->integer('rating')->nullable();
            $table->string('time_start_work', 255);
            $table->string('time_end_work', 255);
            $table->text('next_to')->nullable();
            $table->text('underground')->nullable();
            $table->text('characteristics')->nullable();
            $table->text('phone')->nullable();
            $table->integer('href_img')->default(0);
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
        Schema::dropIfExists('columbaria');
    }
};
