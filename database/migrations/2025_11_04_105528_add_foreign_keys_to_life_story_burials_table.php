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
        Schema::table('life_story_burials', function (Blueprint $table) {
            $table->foreign(['burial_id'])->references(['id'])->on('burials')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('life_story_burials', function (Blueprint $table) {
            $table->dropForeign('life_story_burials_burial_id_foreign');
            $table->dropForeign('life_story_burials_user_id_foreign');
        });
    }
};
