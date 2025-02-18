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
    Schema::create('services', function (Blueprint $table) {
        $table->id();
        $table->text('title');
        $table->text('content');
        $table->unsignedBigInteger('category_id');
        $table->unsignedBigInteger('cemetery_id');
        $table->text('text_under_title')->nullable();
        $table->text('video_1')->nullable();
        $table->text('text_under_video_1')->nullable();
        $table->text('text_under_img')->nullable();
        $table->text('text_sale')->nullable();
        $table->text('text_stages')->nullable();
        $table->text('video_2')->nullable();
        $table->string('img_structure', 255)->nullable();
        $table->timestamps();

        $table->foreign('category_id')->references('id')->on('category_services')->cascadeOnDelete();
        // $table->foreign('cemetery_id')->references('id')->on('cemeteries')->cascadeOnDelete();
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
