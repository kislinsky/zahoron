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
        Schema::create('review_cemeteries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('name');
            $table->text('content');
            $table->integer('status')->default(0);
            $table->integer('rating')->nullable();
            $table->unsignedBigInteger('cemetery_id')->index('review_cemeteries_cemetery_id_foreign');
            $table->unsignedBigInteger('city_id')->nullable()->index('review_cemeteries_city_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_cemeteries');
    }
};
