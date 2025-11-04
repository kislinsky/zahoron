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
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('code');
            $table->text('phone');
            $table->text('token');
            $table->text('role');
            $table->string('inn')->nullable();
            $table->string('okved')->nullable();
            $table->string('contragent')->nullable();
            $table->string('organization_form')->nullable();
            $table->text('organization_ids')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
