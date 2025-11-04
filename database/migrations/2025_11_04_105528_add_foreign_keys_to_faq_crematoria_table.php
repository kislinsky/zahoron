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
        Schema::table('faq_crematoria', function (Blueprint $table) {
            $table->foreign(['crematorium_id'])->references(['id'])->on('crematoria')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faq_crematoria', function (Blueprint $table) {
            $table->dropForeign('faq_crematoria_crematorium_id_foreign');
        });
    }
};
