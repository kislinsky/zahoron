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
    Schema::create('price_product_price_lists', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
        $table->unsignedBigInteger('product_price_list_id');
        $table->unsignedBigInteger('city_id');
        $table->float('price');

        $table->foreign('product_price_list_id')->references('id')->on('product_price_lists')->cascadeOnDelete();
        $table->foreign('city_id')->references('id')->on('cities')->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_product_price_lists');
    }
};
