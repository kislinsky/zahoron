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
        Schema::table('beautifications', function (Blueprint $table) {
            $table->foreign(['burial_id'])->references(['id'])->on('burials')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['cemetery_id'])->references(['id'])->on('cemeteries')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['city_id'])->references(['id'])->on('cities')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['organization_id'])->references(['id'])->on('organizations')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beautifications', function (Blueprint $table) {
            $table->dropForeign('beautifications_burial_id_foreign');
            $table->dropForeign('beautifications_cemetery_id_foreign');
            $table->dropForeign('beautifications_city_id_foreign');
            $table->dropForeign('beautifications_organization_id_foreign');
            $table->dropForeign('beautifications_user_id_foreign');
        });
    }
};
