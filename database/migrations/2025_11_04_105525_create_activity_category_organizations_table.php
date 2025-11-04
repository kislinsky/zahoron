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
        Schema::create('activity_category_organizations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->unsignedBigInteger('organization_id')->index('activity_category_organizations_organization_id_foreign');
            $table->text('category_main_id')->nullable();
            $table->text('category_children_id')->nullable();
            $table->integer('price')->nullable();
            $table->decimal('rating', 5)->nullable();
            $table->text('sales')->nullable();
            $table->text('district_ids')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_category_organizations');
    }
};
