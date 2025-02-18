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
    Schema::create('price_aplications', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
        $table->unsignedBigInteger('type_application_id')->nullable();
        $table->unsignedBigInteger('type_service_id')->nullable();
        $table->unsignedBigInteger('city_id')->nullable();
        $table->integer('price')->nullable();

        $table->foreign('type_application_id')->references('id')->on('type_applications')->cascadeOnDelete();
        $table->foreign('type_service_id')->references('id')->on('type_services')->cascadeOnDelete();
        $table->foreign('city_id')->references('id')->on('cities')->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_aplications');
    }
};
