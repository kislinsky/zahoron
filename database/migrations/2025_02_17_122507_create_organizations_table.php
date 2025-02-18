<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('title');
            $table->text('img_url')->nullable();
            $table->text('img_file')->nullable();
            $table->unsignedBigInteger('city_id');
            $table->integer('all_price')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->text('phone')->nullable();
            $table->text('adres')->nullable();
            $table->string('time_start_work', 255)->nullable();
            $table->string('time_end_work', 255)->nullable();
            $table->text('mini_content')->nullable();
            $table->text('content')->nullable();
            $table->string('name_type', 255)->nullable();
            $table->string('width', 255)->nullable();
            $table->string('longitude', 255)->nullable();
            $table->integer('available_installments')->nullable();
            $table->integer('found_cheaper')->nullable();
            $table->integer('conclusion_contract')->nullable();
            $table->integer('state_compensation')->nullable();
            $table->integer('status')->default(1);
            $table->string('next_to', 255)->nullable();
            $table->string('underground', 255)->nullable();
            $table->float('rating')->nullable();
            $table->text('cemetery_ids')->nullable();
            $table->string('role', 255)->default('organization');
            $table->text('awards')->nullable();
            $table->string('price_list', 255)->nullable();
            $table->string('remains', 255)->nullable();
            $table->text('slug');
            $table->integer('href_img')->default(0);
            $table->string('whatsapp', 255)->nullable();
            $table->string('telegram', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('village', 255)->nullable();
            $table->integer('time_difference')->default(0);
    
            // $table->foreign('city_id')->references('id')->on('cities')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
