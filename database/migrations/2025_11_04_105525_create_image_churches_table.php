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
        Schema::create('image_churches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('church_id')->index('image_churches_church_id_foreign');
            $table->string('img_file')->nullable();
            $table->string('img_url')->nullable();
            $table->integer('href_img')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_churches');
    }
};
