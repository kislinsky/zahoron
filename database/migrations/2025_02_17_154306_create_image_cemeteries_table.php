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
        Schema::create('image_cemeteries', function (Blueprint $table) {
            
            $table->id(); // bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT
            $table->timestamps(); // created_at, updated_at

            $table->text('title')->collation('utf8mb4_unicode_ci'); // text
            
            $table->unsignedBigInteger('cemetery_id'); // ID кладбища
            $table->integer('href_img')->default(0); // int NOT NULL DEFAULT 0

            // Внешний ключ с каскадным удалением
            // $table->foreign('cemetery_id')->references('id')->on('cemeteries')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_cemeteries');
    }
};
