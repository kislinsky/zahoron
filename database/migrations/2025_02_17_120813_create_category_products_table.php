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
    Schema::create('category_products', function (Blueprint $table) {
        $table->id();
        $table->text('title');
        $table->integer('parent_id')->nullable();
        $table->text('icon')->nullable();
        $table->string('icon_white', 255)->nullable();
        $table->string('white_icon', 255)->nullable();
        $table->text('content')->nullable();
        $table->text('manual')->nullable();
        $table->string('manual_video', 255)->nullable();
        $table->string('type', 255)->default('beautification');
        $table->text('additional')->nullable();
        $table->integer('choose_admin')->default(0);
        $table->text('slug');
        $table->string('icon_map', 255)->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_products');
    }
};
