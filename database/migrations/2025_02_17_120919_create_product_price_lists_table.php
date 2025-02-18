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
    Schema::create('product_price_lists', function (Blueprint $table) {
        $table->id();
        $table->string('title', 255);
        $table->integer('price');
        $table->text('excerpt');
        $table->unsignedBigInteger('category_id');
        $table->text('content')->nullable();
        $table->text('text_before_video_1')->nullable();
        $table->text('text_after_video_1')->nullable();
        $table->text('video_1')->nullable();
        $table->text('text_before_videos')->nullable();
        $table->text('text_after_videos')->nullable();
        $table->text('text_images')->nullable();
        $table->text('text_advantages')->nullable();
        $table->text('video_2')->nullable();
        $table->text('text_how_make')->nullable();
        $table->text('title_variants')->nullable();
        $table->text('text_variants')->nullable();
        $table->text('title_advice')->nullable();
        $table->string('icon_white', 255)->nullable();
        $table->string('slug', 255);
        $table->timestamps();

        $table->foreign('category_id')->references('id')->on('category_product_price_lists')->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_price_lists');
    }
};
