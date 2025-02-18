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
    Schema::create('working_hours_columbaria', function (Blueprint $table) {
        $table->id();
        $table->string('time_start_work', 255)->nullable();
        $table->string('time_end_work', 255)->nullable();
        $table->integer('holiday')->default(0);
        $table->unsignedBigInteger('columbarium_id');
        $table->string('day', 255);
        $table->timestamps();

        // $table->foreign('columbarium_id')->references('id')->on('columbaria')->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('working_hours_columbaria');
    }
};
