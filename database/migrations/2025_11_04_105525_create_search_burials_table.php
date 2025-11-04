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
        Schema::create('search_burials', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('name');
            $table->string('surname');
            $table->string('patronymic');
            $table->string('date_birth');
            $table->string('date_death');
            $table->text('location');
            $table->unsignedBigInteger('user_id')->index('search_burials_user_id_foreign');
            $table->integer('status')->default(0);
            $table->text('imgs')->nullable();
            $table->integer('paid')->default(0);
            $table->text('reason_failure')->nullable();
            $table->integer('price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_burials');
    }
};
