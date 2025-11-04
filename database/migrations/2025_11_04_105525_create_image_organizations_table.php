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
        Schema::create('image_organizations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->text('img_url')->nullable();
            $table->text('img_file')->nullable();
            $table->unsignedBigInteger('organization_id');
            $table->integer('href_img')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_organizations');
    }
};
