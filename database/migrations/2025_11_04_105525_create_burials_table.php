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
        Schema::create('burials', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->text('name')->nullable();
            $table->text('surname')->nullable();
            $table->text('patronymic')->nullable();
            $table->text('url')->nullable();
            $table->string('who')->default('Гражданский');
            $table->text('date_death')->nullable();
            $table->text('date_birth')->nullable();
            $table->text('location_death')->nullable();
            $table->text('img_url')->nullable();
            $table->text('img_original_url')->nullable();
            $table->text('information')->nullable();
            $table->string('width')->nullable();
            $table->string('longitude')->nullable();
            $table->unsignedBigInteger('cemetery_id')->nullable();
            $table->text('slug');
            $table->integer('status')->default(1);
            $table->text('photographer')->nullable();
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('decoder_id')->nullable();
            $table->integer('href_img')->default(0);
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->text('img_file')->nullable();
            $table->text('img_original_file')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('burials');
    }
};
