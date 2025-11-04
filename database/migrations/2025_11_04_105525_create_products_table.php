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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->unsignedBigInteger('category_id')->index('products_category_id_foreign');
            $table->integer('category_parent_id')->nullable();
            $table->text('size')->nullable();
            $table->text('content')->nullable();
            $table->text('material')->nullable();
            $table->string('color')->nullable();
            $table->string('status')->nullable();
            $table->integer('view')->default(1);
            $table->integer('price');
            $table->integer('price_sale')->nullable();
            $table->integer('total_price');
            $table->unsignedBigInteger('city_id')->nullable()->index('products_city_id_foreign');
            $table->text('title_institution')->nullable();
            $table->unsignedBigInteger('organization_id')->nullable()->index('products_organization_id_foreign');
            $table->integer('capacity')->nullable();
            $table->text('location_width')->nullable();
            $table->text('location_longitude')->nullable();
            $table->unsignedBigInteger('district_id')->nullable()->index('products_district_id_foreign');
            $table->string('type')->default('product');
            $table->unsignedBigInteger('provider_id')->nullable()->index('products_provider_id_foreign');
            $table->string('layering')->nullable();
            $table->string('cafe')->nullable();
            $table->integer('count_people')->nullable();
            $table->text('slug');
            $table->timestamps();
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
