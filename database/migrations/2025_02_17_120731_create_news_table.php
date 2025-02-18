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
            Schema::create('news', function (Blueprint $table) {
                $table->id();
                $table->text('title');
                $table->text('content');
                $table->text('img');
                $table->unsignedBigInteger('category_id');
                $table->integer('type')->default(1);
                $table->text('slug');
                $table->timestamps();

                $table->foreign('category_id')->references('id')->on('category_news')->onDelete('cascade')->cascadeOnDelete();
            });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
