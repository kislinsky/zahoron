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
            Schema::create('cemeteries', function (Blueprint $table) {
                $table->id();
                $table->text('title');
                $table->text('content')->nullable();
                $table->text('img_url')->nullable();
                $table->text('img_file')->nullable();
                $table->text('adres')->nullable();
                $table->unsignedBigInteger('city_id');
                $table->unsignedBigInteger('area_id')->nullable();
                $table->text('width');
                $table->text('longitude');
                $table->float('rating')->nullable();
                $table->text('mini_content')->nullable();
                $table->text('characteristics')->nullable();
                $table->unsignedBigInteger('district_id')->nullable();
                $table->text('underground')->nullable();
                $table->text('next_to')->nullable();
                $table->float('price_decode')->default(0);
                $table->integer('href_img')->default(0);
                $table->string('village', 255)->nullable();
                $table->string('email', 255)->nullable();
                $table->string('phone', 255)->nullable();
                $table->integer('time_difference')->default(0);
                $table->float('square')->nullable();
                $table->text('responsible')->nullable();
                $table->text('cadastral_number')->nullable();
                $table->integer('cost_sponsorship_call')->default(1000);
                $table->integer('price_burial_location')->default(5900);
                $table->string('date_foundation', 255)->nullable();
                $table->timestamps();

                // $table->foreign('city_id')->references('id')->on('cities')->cascadeOnDelete();
                // $table->foreign('area_id')->references('id')->on('areas')->cascadeOnDelete();
                // $table->foreign('district_id')->references('id')->on('districts')->cascadeOnDelete();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cemeteries');
    }
};
