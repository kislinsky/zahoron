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
    Schema::create('review_cemeteries', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
        $table->string('name', 255);
        $table->text('content');
        $table->integer('status')->default(0);
        $table->integer('rating')->nullable();
        $table->unsignedBigInteger('cemetery_id');
        $table->unsignedBigInteger('city_id')->nullable();

        $table->foreign('cemetery_id')->references('id')->on('cemeteries')->cascadeOnDelete();
        $table->foreign('city_id')->references('id')->on('cities')->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_cemeteries');
    }
};
