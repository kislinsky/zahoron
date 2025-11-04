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
        Schema::create('video_product_price_lists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('video');
            $table->unsignedBigInteger('product_price_list_id')->index('video_product_price_lists_product_price_list_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_product_price_lists');
    }
};
