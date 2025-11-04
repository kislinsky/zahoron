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
        Schema::create('reviews_organizations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->unsignedBigInteger('organization_id')->index('reviews_organizations_organization_id_foreign');
            $table->string('name');
            $table->text('content');
            $table->integer('status')->default(0);
            $table->unsignedBigInteger('city_id')->index('reviews_organizations_city_id_foreign');
            $table->integer('rating')->nullable();
            $table->text('organization_response')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews_organizations');
    }
};
