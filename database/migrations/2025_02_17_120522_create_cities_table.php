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
            Schema::create('cities', function (Blueprint $table) {
                $table->id();
                $table->string('title', 255);
                $table->string('slug', 255)->default('');
                $table->unsignedBigInteger('edge_id');
                $table->unsignedBigInteger('area_id');
                $table->integer('selected_admin')->nullable();
                $table->integer('selected_form')->default(0);
                $table->text('width')->nullable();
                $table->text('longitude')->nullable();
                $table->string('text_about_project', 5000)->default('...');
                $table->string('text_how_properly_arrange_funeral_services', 5000)->default('...');
                $table->text('content_mortuary')->nullable();
                $table->timestamps();

                $table->foreign('edge_id')->references('id')->on('edges')->cascadeOnDelete();
                // $table->foreign('area_id')->references('id')->on('areas')->cascadeOnDelete();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
