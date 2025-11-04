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
        Schema::create('product_request_to_suppliers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->text('title');
            $table->text('content');
            $table->unsignedBigInteger('category_id')->nullable()->index('product_request_to_suppliers_category_id_foreign');
            $table->longText('images');
            $table->integer('delivery')->default(0);
            $table->integer('price_delivery')->nullable();
            $table->unsignedBigInteger('organization_id')->index('product_request_to_suppliers_organization_id_foreign');
            $table->unsignedBigInteger('organization_provider_id')->nullable()->index('product_request_to_suppliers_organization_provider_id_foreign');
            $table->integer('status')->default(0);
            $table->string('name_delivery')->nullable();
            $table->string('term')->nullable();
            $table->integer('price_product')->nullable();
            $table->text('answer')->nullable();
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
