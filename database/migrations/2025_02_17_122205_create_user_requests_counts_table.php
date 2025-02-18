<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('user_requests_counts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_application_id');
            $table->unsignedBigInteger('type_service_id');
            $table->unsignedBigInteger('organization_id');
            $table->integer('count');
            $table->timestamps();
    
            // $table->foreign('type_application_id')->references('id')->on('type_applications')->cascadeOnDelete();
            // $table->foreign('type_service_id')->references('id')->on('type_services')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_requests_counts');
    }
};
