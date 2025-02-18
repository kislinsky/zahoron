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
    Schema::create('dead_applications', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
        $table->unsignedBigInteger('city_id');
        $table->text('fio');
        $table->unsignedBigInteger('mortuary_id')->nullable();
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('organization_id')->nullable();
        $table->text('call_time')->nullable();
        $table->integer('status')->default(0);

        $table->foreign('city_id')->references('id')->on('cities')->cascadeOnDelete();
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
        Schema::dropIfExists('dead_applications');
    }
};
