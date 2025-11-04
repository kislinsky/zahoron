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
        Schema::create('review_columbaria', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->text('name');
            $table->text('content');
            $table->integer('rating')->nullable();
            $table->unsignedBigInteger('columbarium_id')->index('review_columbaria_columbarium_id_foreign');
            $table->integer('status')->default(0);
            $table->unsignedBigInteger('city_id')->nullable()->index('review_columbaria_city_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_columbaria');
    }
};
