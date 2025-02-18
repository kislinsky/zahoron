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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->unsignedBigInteger('category_id');
            $table->integer('category_parent_id')->nullable();
            $table->text('size')->nullable();
            $table->text('content')->nullable();
            $table->text('material')->nullable();
            $table->string('color', 255)->nullable();
            $table->string('status', 255)->nullable();
            $table->integer('price');
            $table->integer('price_sale')->nullable();
            $table->integer('total_price');
            $table->unsignedBigInteger('city_id')->nullable();
            $table->text('title_institution')->nullable();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->integer('capacity')->nullable();
            $table->text('location_width')->nullable();
            $table->text('location_longitude')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->string('type', 255)->default('product');
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->string('layering', 255)->nullable();
            $table->string('cafe', 255)->nullable();
            $table->integer('count_people')->nullable();
            $table->text('slug');
            $table->timestamps();
    
            $table->foreign('category_id')->references('id')->on('category_products')->cascadeOnDelete();
            $table->foreign('city_id')->references('id')->on('cities')->cascadeOnDelete();
            $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
            $table->foreign('district_id')->references('id')->on('districts')->cascadeOnDelete();
            $table->foreign('provider_id')->references('id')->on('organizations')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
