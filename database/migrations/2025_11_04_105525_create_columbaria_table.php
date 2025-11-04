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
        Schema::create('columbaria', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title');
            $table->text('slug');
            $table->unsignedBigInteger('city_id');
            $table->string('width');
            $table->string('longitude');
            $table->text('content');
            $table->text('mini_content');
            $table->string('img_url')->nullable();
            $table->text('img_file')->nullable();
            $table->text('adres');
            $table->integer('rating')->nullable();
            $table->text('next_to')->nullable();
            $table->text('underground')->nullable();
            $table->text('characteristics')->nullable();
            $table->text('phone')->nullable();
            $table->integer('href_img')->default(0);
            $table->text('two_gis_link')->nullable();
            $table->text('url_site')->nullable();
            $table->integer('time_difference')->default(0);
            $table->timestamps();
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
