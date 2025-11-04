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
        Schema::create('beautifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->unsignedBigInteger('burial_id')->nullable()->index('beautifications_burial_id_foreign');
            $table->unsignedBigInteger('user_id')->index('beautifications_user_id_foreign');
            $table->text('products_id')->nullable();
            $table->unsignedBigInteger('organization_id')->nullable()->index('beautifications_organization_id_foreign');
            $table->unsignedBigInteger('cemetery_id')->nullable()->index('beautifications_cemetery_id_foreign');
            $table->integer('status')->default(0);
            $table->unsignedBigInteger('city_id')->index('beautifications_city_id_foreign');
            $table->text('call_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beautifications');
    }
};
