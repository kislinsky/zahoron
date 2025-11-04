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
        Schema::create('info_edit_burials', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->unsignedBigInteger('burial_id')->index('info_edit_burials_burial_id_foreign');
            $table->unsignedBigInteger('user_id')->index('info_edit_burials_user_id_foreign');
            $table->string('name');
            $table->string('surname');
            $table->string('patronymic');
            $table->string('date_birth');
            $table->string('date_death');
            $table->string('who');
            $table->integer('status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('info_edit_burials');
    }
};
