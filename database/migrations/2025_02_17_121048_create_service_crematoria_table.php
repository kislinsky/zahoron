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
    Schema::create('service_crematoria', function (Blueprint $table) {
        $table->id();
        $table->string('title', 250);
        $table->integer('price')->nullable();
        $table->unsignedBigInteger('crematorium_id');
        $table->timestamps();

        $table->foreign('crematorium_id')->references('id')->on('crematoria')->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_crematoria');
    }
};
