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
        Schema::table('dead_applications', function (Blueprint $table) {
            $table->foreign(['city_id'])->references(['id'])->on('cities')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['mortuary_id'])->references(['id'])->on('mortuaries')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['organization_id'])->references(['id'])->on('organizations')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dead_applications', function (Blueprint $table) {
            $table->dropForeign('dead_applications_city_id_foreign');
            $table->dropForeign('dead_applications_mortuary_id_foreign');
            $table->dropForeign('dead_applications_organization_id_foreign');
            $table->dropForeign('dead_applications_user_id_foreign');
        });
    }
};
