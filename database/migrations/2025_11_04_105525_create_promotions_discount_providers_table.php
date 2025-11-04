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
        Schema::create('promotions_discount_providers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->text('condition');
            $table->unsignedBigInteger('organization_id')->index('promotions_discount_providers_organization_id_foreign');
            $table->integer('procent')->nullable();
            $table->string('time_action');
            $table->string('type');
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
