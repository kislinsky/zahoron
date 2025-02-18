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
    Schema::create('burials', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
        $table->text('name')->nullable();
        $table->text('surname')->nullable();
        $table->text('patronymic')->nullable();
        $table->string('who', 255)->default('Гражданский');
        $table->text('date_death')->nullable();
        $table->text('date_birth')->nullable();
        $table->text('location_death')->nullable();
        $table->text('img')->nullable();
        $table->text('img_original')->nullable();
        $table->text('information')->nullable();
        $table->string('width', 255)->nullable();
        $table->string('longitude', 255)->nullable();
        $table->unsignedBigInteger('cemetery_id')->nullable();
        $table->text('slug');
        $table->integer('status')->default(1);
        $table->text('photographer')->nullable();
        $table->text('comment')->nullable();
        $table->unsignedBigInteger('decoder_id')->nullable();
        $table->integer('href_img')->default(0);
        $table->unsignedBigInteger('agent_id')->nullable();

        // $table->foreign('cemetery_id')->references('id')->on('cemeteries')->cascadeOnDelete();
        // $table->foreign('decoder_id')->references('id')->on('users')->cascadeOnDelete();
        // $table->foreign('agent_id')->references('id')->on('users')->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('burials');
    }
};
