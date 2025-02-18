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
    Schema::create('beautifications', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
        $table->unsignedBigInteger('burial_id')->nullable();
        $table->unsignedBigInteger('user_id');
        $table->text('products_id')->nullable();
        $table->unsignedBigInteger('organization_id')->nullable();
        $table->unsignedBigInteger('cemetery_id')->nullable();
        $table->integer('status')->default(0);
        $table->unsignedBigInteger('city_id');
        $table->text('call_time')->nullable();

        $table->foreign('burial_id')->references('id')->on('burials')->cascadeOnDelete();
        $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
        $table->foreign('cemetery_id')->references('id')->on('cemeteries')->cascadeOnDelete();
        $table->foreign('city_id')->references('id')->on('cities')->cascadeOnDelete();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beautifications');
    }
};
