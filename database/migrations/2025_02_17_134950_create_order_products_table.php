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
    Schema::create('order_products', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
        $table->string('additional', 255)->nullable();
        $table->unsignedBigInteger('product_id');
        $table->unsignedBigInteger('user_id');
        $table->string('customer_comment', 400)->nullable();
        $table->integer('count');
        $table->integer('price');
        $table->string('size', 255)->nullable();
        $table->integer('status')->default(0);
        $table->unsignedBigInteger('cemetery_id')->nullable();
        $table->date('date')->nullable();
        $table->string('time', 255)->nullable();
        $table->unsignedBigInteger('mortuary_id')->nullable();
        $table->string('city_from', 255)->nullable();
        $table->string('city_to', 255)->nullable();
        $table->integer('all_price')->nullable();
        $table->unsignedBigInteger('organization_id')->nullable();

        $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        $table->foreign('cemetery_id')->references('id')->on('cemeteries')->cascadeOnDelete();
        $table->foreign('mortuary_id')->references('id')->on('mortuaries')->cascadeOnDelete();
        $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};
