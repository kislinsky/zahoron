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
        Schema::create('acfs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('name');
            $table->text('content_html')->nullable();
            $table->text('content_plain')->nullable();
            $table->text('file')->nullable();
            $table->string('type')->nullable()->default('text');
            $table->integer('is_plain_text')->nullable();
            $table->unsignedBigInteger('page_id')->index('acfs_page_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acfs');
    }
};
