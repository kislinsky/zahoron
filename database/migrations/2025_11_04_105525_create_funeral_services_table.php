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
        Schema::create('funeral_services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('service');
            $table->unsignedBigInteger('city_id')->index('funeral_services_city_id_foreign');
            $table->unsignedBigInteger('city_id_to')->nullable()->index('funeral_services_city_id_to_foreign');
            $table->unsignedBigInteger('cemetery_id')->nullable()->index('funeral_services_cemetery_id_foreign');
            $table->unsignedBigInteger('mortuary_id')->nullable()->index('funeral_services_mortuary_id_foreign');
            $table->text('status_death');
            $table->text('civilian_status_death');
            $table->unsignedBigInteger('funeral_service_church')->nullable();
            $table->unsignedBigInteger('farewell_hall')->nullable();
            $table->unsignedBigInteger('user_id')->index('funeral_services_user_id_foreign');
            $table->unsignedBigInteger('organization_id')->nullable()->index('funeral_services_organization_id_foreign');
            $table->integer('status')->default(0);
            $table->text('call_time')->nullable();
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
