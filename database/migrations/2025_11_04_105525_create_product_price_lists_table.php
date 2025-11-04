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
        Schema::create('product_price_lists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->integer('price');
            $table->text('excerpt');
            $table->unsignedBigInteger('category_id')->index('product_price_lists_category_id_foreign');
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
            $table->string('icon_white')->nullable();
            $table->string('slug');
            $table->timestamps();
            $table->integer('view')->default(1);
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
