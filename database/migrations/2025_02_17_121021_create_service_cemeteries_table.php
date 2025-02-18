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
    Schema::create('service_cemeteries', function (Blueprint $table) {
        $table->id();
        $table->string('title', 255);
        $table->integer('price')->nullable();
        $table->unsignedBigInteger('cemetery_id');
        $table->timestamps();

        $table->foreign('cemetery_id')->references('id')->on('cemeteries')->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_cemeteries');
    }
};
