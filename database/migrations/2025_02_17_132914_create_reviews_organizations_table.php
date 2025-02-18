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
    Schema::create('reviews_organizations', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
        $table->unsignedBigInteger('organization_id');
        $table->string('name', 255);
        $table->text('content');
        $table->integer('status')->default(0);
        $table->unsignedBigInteger('city_id');
        $table->integer('rating')->nullable();
        $table->text('organization_response')->nullable();

        $table->foreign('organization_id')->references('id')->on('organizations');
        $table->foreign('city_id')->references('id')->on('cities');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews_organizations');
    }
};
