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
        Schema::create('mosques', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title');
            $table->text('slug');
            $table->unsignedBigInteger('city_id')->index('mosques_city_id_foreign');
            $table->text('latitude')->nullable();
            $table->text('longitude')->nullable();
            $table->text('content')->nullable();
            $table->text('mini_content')->nullable();
            $table->string('img_url')->nullable();
            $table->text('img_file')->nullable();
            $table->text('address');
            $table->integer('rating')->nullable();
            $table->text('next_to')->nullable();
            $table->text('underground')->nullable();
            $table->text('characteristics')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->string('phone', 250)->nullable();
            $table->integer('href_img')->default(0);
            $table->text('url_site')->nullable();
            $table->text('two_gis_link')->nullable();
            $table->string('village')->nullable();
            $table->string('email')->nullable();
            $table->integer('time_difference')->default(0);
            $table->text('inn')->nullable();
            $table->text('documents')->nullable();
            $table->text('pathologist')->nullable();
            $table->text('responsible_organization')->nullable();
            $table->text('address_responsible_person')->nullable();
            $table->text('responsible_person_full_name')->nullable();
            $table->text('okved')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mosques');
    }
};
