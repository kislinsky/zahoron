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
    Schema::create('funeral_services', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
        $table->integer('service');
        $table->unsignedBigInteger('city_id');
        $table->unsignedBigInteger('city_id_to')->nullable();
        $table->unsignedBigInteger('cemetery_id')->nullable();
        $table->unsignedBigInteger('mortuary_id')->nullable();
        $table->text('status_death');
        $table->text('civilian_status_death');
        $table->unsignedBigInteger('funeral_service_church')->nullable();
        $table->unsignedBigInteger('farewell_hall')->nullable();
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('organization_id')->nullable();
        $table->integer('status')->default(0);
        $table->text('call_time')->nullable();

        $table->foreign('city_id')->references('id')->on('cities')->cascadeOnDelete();
        $table->foreign('city_id_to')->references('id')->on('cities')->cascadeOnDelete();
        $table->foreign('cemetery_id')->references('id')->on('cemeteries')->cascadeOnDelete();
        $table->foreign('mortuary_id')->references('id')->on('mortuaries')->cascadeOnDelete();
        $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funeral_services');
    }
};
