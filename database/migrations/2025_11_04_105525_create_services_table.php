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
        Schema::create('services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title');
            $table->text('content');
            $table->unsignedBigInteger('category_id')->nullable()->index('services_category_id_foreign');
            $table->unsignedBigInteger('cemetery_id')->nullable();
            $table->text('text_under_title')->nullable();
            $table->text('video_1')->nullable();
            $table->text('text_under_video_1')->nullable();
            $table->text('text_under_img')->nullable();
            $table->text('text_sale')->nullable();
            $table->text('text_stages')->nullable();
            $table->text('video_2')->nullable();
            $table->string('img_structure')->nullable();
            $table->timestamps();
            $table->integer('price');
            $table->text('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
