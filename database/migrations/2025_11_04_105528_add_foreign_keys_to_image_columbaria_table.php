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
        Schema::table('image_columbaria', function (Blueprint $table) {
            $table->foreign(['columbarium_id'])->references(['id'])->on('columbaria')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('image_columbaria', function (Blueprint $table) {
            $table->dropForeign('image_columbaria_columbarium_id_foreign');
        });
    }
};
