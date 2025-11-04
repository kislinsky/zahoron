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
        Schema::create('cemeteries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title');
            $table->text('slug');
            $table->text('content')->nullable();
            $table->text('img_url')->nullable();
            $table->text('img_file')->nullable();
            $table->text('adres')->nullable();
            $table->text('responsible_person_address')->nullable();
            $table->text('responsible_organization')->nullable();
            $table->text('okved')->nullable();
            $table->bigInteger('inn')->nullable();
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('area_id')->nullable();
            $table->text('width');
            $table->text('longitude');
            $table->double('rating', 8, 2)->nullable();
            $table->text('mini_content')->nullable();
            $table->text('characteristics')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->text('underground')->nullable();
            $table->text('next_to')->nullable();
            $table->double('price_decode', 8, 2)->default(0);
            $table->integer('href_img')->default(0);
            $table->string('village')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->integer('time_difference')->default(0);
            $table->text('square')->nullable();
            $table->text('responsible')->nullable();
            $table->text('cadastral_number')->nullable();
            $table->integer('cost_sponsorship_call')->default(1000);
            $table->integer('price_burial_location')->default(5900);
            $table->string('date_foundation')->nullable();
            $table->text('two_gis_link')->nullable();
            $table->timestamps();
            $table->integer('status')->default(1);
            $table->integer('priority')->default(0);
            $table->text('address_responsible_person')->nullable();
            $table->text('responsible_person_full_name')->nullable();
            $table->boolean('show_img')->nullable()->default(true);
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
