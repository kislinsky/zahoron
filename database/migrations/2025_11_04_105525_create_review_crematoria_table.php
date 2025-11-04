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
        Schema::create('review_crematoria', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('name', 250);
            $table->text('content');
            $table->integer('status')->default(0);
            $table->unsignedBigInteger('crematorium_id')->index('review_crematoria_crematorium_id_foreign');
            $table->integer('rating');
            $table->unsignedBigInteger('city_id')->nullable()->index('review_crematoria_city_id_foreign');
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
