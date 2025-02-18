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
    Schema::create('tasks', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
        $table->text('title');
        $table->unsignedBigInteger('user_id');
        $table->float('price');
        $table->integer('status')->default(0);
        $table->unsignedBigInteger('burial_id')->nullable();
        $table->integer('count')->default(0);

        $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        $table->foreign('burial_id')->references('id')->on('burials')->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
