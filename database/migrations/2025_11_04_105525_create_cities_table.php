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
        Schema::create('cities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('slug')->default('')->index();
            $table->unsignedBigInteger('edge_id')->index('cities_edge_id_foreign');
            $table->unsignedBigInteger('area_id');
            $table->integer('selected_admin')->nullable()->index();
            $table->integer('selected_form')->default(0);
            $table->text('width')->nullable();
            $table->text('longitude')->nullable();
            $table->integer('utc_offset')->nullable();
            $table->string('text_about_project', 5000)->default('...');
            $table->string('text_how_properly_arrange_funeral_services', 5000)->default('...');
            $table->text('content_mortuary')->nullable();
            $table->string('limit_calls')->nullable();
            $table->timestamps();
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
