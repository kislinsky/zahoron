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
        Schema::create('order_services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->unsignedBigInteger('burial_id')->index('order_services_burial_id_foreign');
            $table->unsignedBigInteger('user_id')->index('order_services_user_id_foreign');
            $table->string('services_id');
            $table->integer('status')->default(0);
            $table->string('size');
            $table->string('date_pay')->nullable();
            $table->text('imgs')->nullable();
            $table->text('customer_comment')->nullable();
            $table->unsignedBigInteger('worker_id')->nullable()->index('order_services_worker_id_foreign');
            $table->unsignedBigInteger('cemetery_id')->nullable()->index('order_services_cemetery_id_foreign');
            $table->double('price', 8, 2);
            $table->integer('paid')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_services');
    }
};
