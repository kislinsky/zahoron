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
        Schema::table('funeral_services', function (Blueprint $table) {
            $table->foreign(['cemetery_id'])->references(['id'])->on('cemeteries')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['city_id'])->references(['id'])->on('cities')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['city_id_to'])->references(['id'])->on('cities')->onUpdate('no action')->onDelete('cascade');
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
        Schema::table('funeral_services', function (Blueprint $table) {
            $table->dropForeign('funeral_services_cemetery_id_foreign');
            $table->dropForeign('funeral_services_city_id_foreign');
            $table->dropForeign('funeral_services_city_id_to_foreign');
            $table->dropForeign('funeral_services_mortuary_id_foreign');
            $table->dropForeign('funeral_services_organization_id_foreign');
            $table->dropForeign('funeral_services_user_id_foreign');
        });
    }
};
