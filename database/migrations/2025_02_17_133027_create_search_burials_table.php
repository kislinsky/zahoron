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
    Schema::create('search_burials', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
        $table->string('name', 255);
        $table->string('surname', 255);
        $table->string('patronymic', 255);
        $table->string('date_birth', 255);
        $table->string('date_death', 255);
        $table->text('location');
        $table->unsignedBigInteger('user_id');
        $table->integer('status')->default(0);
        $table->text('imgs')->nullable();
        $table->integer('paid')->default(0);
        $table->text('reason_failure')->nullable();
        $table->integer('price')->nullable();

        $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_burials');
    }
};
