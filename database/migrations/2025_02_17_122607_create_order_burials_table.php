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
    Schema::create('order_burials', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
        $table->unsignedBigInteger('burial_id');
        $table->unsignedBigInteger('user_id');
        $table->integer('status')->default(0);
        $table->text('customer_comment')->nullable();
        $table->float('price');
        $table->text('date_pay')->nullable();

        $table->foreign('burial_id')->references('id')->on('burials')->cascadeOnDelete();
        $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_burials');
    }
};
