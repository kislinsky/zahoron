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
    Schema::create('price_list_organizations', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
        $table->unsignedBigInteger('organization_id');
        $table->text('title');
        $table->text('file_name');
        $table->string('type', 255);

        $table->foreign('organization_id')->references('id')->on('organizations')->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_list_organizations');
    }
};
