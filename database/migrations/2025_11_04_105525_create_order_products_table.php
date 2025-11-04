<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('additional')->nullable();
            $table->unsignedBigInteger('product_id')->index('order_products_product_id_foreign');
            $table->unsignedBigInteger('user_id')->index('order_products_user_id_foreign');
            $table->string('customer_comment', 400)->nullable();
            $table->integer('count');
            $table->integer('price');
            $table->string('size')->nullable();
            $table->integer('status')->default(0);
            $table->unsignedBigInteger('cemetery_id')->nullable()->index('order_products_cemetery_id_foreign');
            $table->date('date')->nullable();
            $table->string('time')->nullable();
            $table->unsignedBigInteger('mortuary_id')->nullable()->index('order_products_mortuary_id_foreign');
            $table->string('city_from')->nullable();
            $table->string('city_to')->nullable();
            $table->integer('all_price')->nullable();
            $table->unsignedBigInteger('organization_id')->nullable()->index('order_products_organization_id_foreign');
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
