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
        Schema::create('variant_product_price_lists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title');
            $table->text('img');
            $table->unsignedBigInteger('product_price_list_id')->index('variant_product_price_lists_product_price_list_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_product_price_lists');
    }
};
