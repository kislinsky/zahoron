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
        Schema::table('advice_product_price_lists', function (Blueprint $table) {
            $table->foreign(['product_price_list_id'])->references(['id'])->on('product_price_lists')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advice_product_price_lists', function (Blueprint $table) {
            $table->dropForeign('advice_product_price_lists_product_price_list_id_foreign');
        });
    }
};
