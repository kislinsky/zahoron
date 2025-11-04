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
        Schema::table('image_mosques', function (Blueprint $table) {
            $table->foreign(['mosque_id'])->references(['id'])->on('mosques')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('image_mosques', function (Blueprint $table) {
            $table->dropForeign('image_mosques_mosque_id_foreign');
        });
    }
};
