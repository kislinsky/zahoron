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
    Schema::create('s_e_o_s', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
        $table->text('page');
        $table->text('name');
        $table->text('title');
        $table->text('content');
        $table->unsignedBigInteger('seo_object_id');

        $table->foreign('seo_object_id')->references('id')->on('seo_objects')->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('s_e_o_s');
    }
};
