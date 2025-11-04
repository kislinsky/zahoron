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
        Schema::create('memorials', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->unsignedBigInteger('city_id')->index('memorials_city_id_foreign');
            $table->unsignedBigInteger('district_id')->index('memorials_district_id_foreign');
            $table->date('date');
            $table->text('time');
            $table->integer('count');
            $table->unsignedBigInteger('user_id')->index('memorials_user_id_foreign');
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->integer('status')->default(0);
            $table->integer('count_time');
            $table->text('call_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memorials');
    }
};
