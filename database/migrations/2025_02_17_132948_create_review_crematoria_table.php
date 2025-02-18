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
        Schema::create('review_crematoria', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name', 250);
            $table->text('content');
            $table->integer('status')->default(0);
            $table->unsignedBigInteger('crematorium_id');
            $table->integer('rating');
            $table->unsignedBigInteger('city_id')->nullable();
    
            $table->foreign('crematorium_id')->references('id')->on('crematoria')->cascadeOnDelete();
            $table->foreign('city_id')->references('id')->on('cities')->cascadeOnDelete();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_crematoria');
    }
};
