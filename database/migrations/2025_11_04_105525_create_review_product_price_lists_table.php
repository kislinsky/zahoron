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
        Schema::create('review_product_price_lists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->index('review_product_price_lists_user_id_foreign');
            $table->string('img_before');
            $table->string('img_after');
            $table->text('content');
            $table->unsignedBigInteger('product_price_list_id')->index('review_product_price_lists_product_price_list_id_foreign');
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
