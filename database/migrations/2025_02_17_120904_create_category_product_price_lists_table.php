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
            Schema::create('category_product_price_lists', function (Blueprint $table) {
                $table->id();
                $table->string('title', 255);
                $table->string('icon', 255)->nullable();
                $table->text('content');
                $table->string('video', 255)->nullable();
                $table->integer('parent_id')->nullable();
                $table->text('slug');
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_product_price_lists');
    }
};
