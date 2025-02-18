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
    Schema::create('our_works', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('category_id');
        $table->text('img_before');
        $table->text('img_after');
        $table->timestamps();

        $table->foreign('category_id')->references('id')->on('category_our_works')->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('our_works');
    }
};
