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
        Schema::create('promotions_discount_providers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('condition');
            $table->unsignedBigInteger('organization_id');
            $table->integer('procent')->nullable();
            $table->string('time_action', 255);
            $table->string('type', 255);
    
            $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions_discount_providers');
    }
};
