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
        Schema::create('dead_applications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->unsignedBigInteger('city_id')->index('dead_applications_city_id_foreign');
            $table->text('fio');
            $table->unsignedBigInteger('mortuary_id')->nullable()->index('dead_applications_mortuary_id_foreign');
            $table->unsignedBigInteger('user_id')->index('dead_applications_user_id_foreign');
            $table->unsignedBigInteger('organization_id')->nullable()->index('dead_applications_organization_id_foreign');
            $table->text('call_time')->nullable();
            $table->integer('status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dead_applications');
    }
};
