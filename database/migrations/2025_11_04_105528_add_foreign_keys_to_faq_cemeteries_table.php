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
        Schema::table('faq_cemeteries', function (Blueprint $table) {
            $table->foreign(['cemetery_id'])->references(['id'])->on('cemeteries')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faq_cemeteries', function (Blueprint $table) {
            $table->dropForeign('faq_cemeteries_cemetery_id_foreign');
        });
    }
};
