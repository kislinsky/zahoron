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
        Schema::create('stage_product_price_lists', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->text('content');
            $table->unsignedBigInteger('product_price_list_id');
            $table->timestamps();
    
            $table->foreign('product_price_list_id')->references('id')->on('product_price_lists')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stage_product_price_lists');
    }
};
