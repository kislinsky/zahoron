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
        Schema::create('category_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title');
            $table->integer('parent_id')->nullable();
            $table->text('icon')->nullable();
            $table->string('icon_white')->nullable();
            $table->text('content')->nullable();
            $table->text('manual')->nullable();
            $table->string('manual_video')->nullable();
            $table->string('type')->default('beautification');
            $table->text('additional')->nullable();
            $table->integer('choose_admin')->default(0);
            $table->text('slug');
            $table->string('icon_map')->nullable();
            $table->integer('display')->default(1);
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
