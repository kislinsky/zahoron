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
        Schema::create('words_memories', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->unsignedBigInteger('burial_id');
            $table->string('img', 255)->nullable();
            $table->integer('status')->default(0);
            $table->timestamps();
    
            $table->foreign('burial_id')->references('id')->on('burials')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('words_memories');
    }
};
