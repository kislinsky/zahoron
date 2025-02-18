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
    Schema::create('product_request_to_suppliers', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
        $table->text('title');
        $table->text('content');
        $table->unsignedBigInteger('category_id')->nullable();
        $table->longText('images');
        $table->integer('delivery')->default(0);
        $table->integer('price_delivery')->nullable();
        $table->unsignedBigInteger('organization_id');
        $table->unsignedBigInteger('organization_provider_id')->nullable();
        $table->integer('status')->default(0);
        $table->string('name_delivery', 255)->nullable();
        $table->string('term', 255)->nullable();
        $table->integer('price_product')->nullable();
        $table->text('answer')->nullable();

        $table->foreign('category_id')->references('id')->on('category_products')->cascadeOnDelete();
        $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
        $table->foreign('organization_provider_id')->references('id')->on('organizations')->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_request_to_suppliers');
    }
};
