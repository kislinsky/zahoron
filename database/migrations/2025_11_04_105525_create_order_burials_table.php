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
        Schema::create('order_burials', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->unsignedBigInteger('burial_id')->index('order_burials_burial_id_foreign');
            $table->unsignedBigInteger('user_id')->index('order_burials_user_id_foreign');
            $table->integer('status')->default(0);
            $table->text('customer_comment')->nullable();
            $table->double('price', 8, 2);
            $table->text('date_pay')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_burials');
    }
};
