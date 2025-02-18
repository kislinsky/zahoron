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
        Schema::create('faq_services', function (Blueprint $table) {
            $table->id(); // bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT
            $table->timestamps(); // created_at, updated_at

            $table->text('title')->collation('utf8mb4_unicode_ci'); // utf8mb4 для поддержки Unicode
            $table->text('content')->collation('utf8mb4_unicode_ci');

            $table->unsignedBigInteger('service_id'); // service_id как bigint UNSIGNED
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faq_services');
    }
};
