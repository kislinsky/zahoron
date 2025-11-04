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
        Schema::create('requests_cost_products_suppliers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('organization_provider_id')->nullable();
            $table->longText('products');
            $table->text('transport_companies');
            $table->integer('status')->default(1);
            $table->longText('categories_provider_product');
            $table->integer('price')->nullable();
            $table->integer('price_transport_companies')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests_cost_products_suppliers');
    }
};
