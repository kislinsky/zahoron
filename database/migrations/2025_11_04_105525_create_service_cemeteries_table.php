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
        Schema::create('service_cemeteries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->integer('price')->nullable();
            $table->unsignedBigInteger('cemetery_id')->index('service_cemeteries_cemetery_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_cemeteries');
    }
};
