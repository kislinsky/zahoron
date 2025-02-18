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
    Schema::create('category_product_providers', function (Blueprint $table) {
        $table->id();
        $table->text('title');
        $table->string('icon', 255)->nullable();
        $table->string('icon_white', 255)->nullable();
        $table->integer('parent_id')->nullable();
        $table->integer('choose_admin')->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_product_providers');
    }
};
