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
    Schema::create('order_services', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
        $table->unsignedBigInteger('burial_id');
        $table->unsignedBigInteger('user_id');
        $table->string('services_id', 255);
        $table->integer('status')->default(0);
        $table->string('size', 255);
        $table->string('date_pay', 255)->nullable();
        $table->text('imgs')->nullable();
        $table->text('customer_comment')->nullable();
        $table->unsignedBigInteger('worker_id')->nullable();
        $table->unsignedBigInteger('cemetery_id')->nullable();
        $table->float('price');
        $table->integer('paid')->default(0);

        $table->foreign('burial_id')->references('id')->on('burials')->cascadeOnDelete();
        $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        $table->foreign('worker_id')->references('id')->on('users')->cascadeOnDelete();
        $table->foreign('cemetery_id')->references('id')->on('cemeteries')->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_services');
    }
};
