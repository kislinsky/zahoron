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
        Schema::table('image_churches', function (Blueprint $table) {
            $table->foreign(['church_id'])->references(['id'])->on('churches')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('image_churches', function (Blueprint $table) {
            $table->dropForeign('image_churches_church_id_foreign');
        });
    }
};
