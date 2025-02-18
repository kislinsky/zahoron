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
    Schema::create('review_product_price_lists', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
        $table->unsignedBigInteger('user_id');
        $table->string('img_before', 255);
        $table->string('img_after', 255);
        $table->text('content');
        $table->unsignedBigInteger('product_price_list_id');

        $table->foreign('user_id')->references('id')->on('users');
        $table->foreign('product_price_list_id')->references('id')->on('product_price_lists')->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_product_price_lists');
    }
};
