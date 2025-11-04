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
        Schema::create('price_aplications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->unsignedBigInteger('type_application_id')->nullable()->index('price_aplications_type_application_id_foreign');
            $table->unsignedBigInteger('type_service_id')->nullable()->index('price_aplications_type_service_id_foreign');
            $table->unsignedBigInteger('city_id')->nullable()->index('price_aplications_city_id_foreign');
            $table->integer('price')->nullable();
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
