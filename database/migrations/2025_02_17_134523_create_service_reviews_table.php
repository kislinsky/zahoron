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
        Schema::create('service_reviews', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->string('img_before', 255);
            $table->string('img_after', 255);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('service_id');
            $table->timestamps();
    
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('service_id')->references('id')->on('services')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_reviews');
    }
};
