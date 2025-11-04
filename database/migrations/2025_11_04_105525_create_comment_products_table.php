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
        Schema::create('comment_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->text('content');
            $table->unsignedBigInteger('product_id')->index('comment_products_product_id_foreign');
            $table->string('name');
            $table->string('surname');
            $table->integer('status')->default(0);
            $table->unsignedBigInteger('category_id')->index('comment_products_category_id_foreign');
            $table->unsignedBigInteger('organization_id')->index('comment_products_organization_id_foreign');
            $table->text('organization_response')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_products');
    }
};
