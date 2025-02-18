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
    Schema::create('price_services', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
        $table->unsignedBigInteger('cemetery_id');
        $table->unsignedBigInteger('service_id');
        $table->float('price');

        $table->foreign('cemetery_id')->references('id')->on('cemeteries')->cascadeOnDelete();
        $table->foreign('service_id')->references('id')->on('services')->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_services');
    }
};
