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
        Schema::table('faq_category_products', function (Blueprint $table) {
            $table->foreign(['category_id'])->references(['id'])->on('category_products')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faq_category_products', function (Blueprint $table) {
            $table->dropForeign('faq_category_products_category_id_foreign');
        });
    }
};
