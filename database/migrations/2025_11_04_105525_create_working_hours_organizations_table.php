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
        Schema::create('working_hours_organizations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('time_start_work')->nullable();
            $table->string('time_end_work')->nullable();
            $table->integer('holiday')->default(0);
            $table->unsignedBigInteger('organization_id')->index('working_hours_organizations_organization_id_foreign');
            $table->string('day');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('working_hours_organizations');
    }
};
