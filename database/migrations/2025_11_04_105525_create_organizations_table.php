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
        Schema::create('organizations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('title');
            $table->text('link_website')->nullable();
            $table->text('img_url')->nullable();
            $table->text('img_file')->nullable();
            $table->text('img_main_file')->nullable();
            $table->text('img_main_url')->nullable();
            $table->unsignedBigInteger('city_id');
            $table->integer('all_price')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->text('phone')->nullable();
            $table->text('adres')->nullable();
            $table->string('nearby')->nullable();
            $table->string('time_start_work')->nullable();
            $table->string('time_end_work')->nullable();
            $table->text('mini_content')->nullable();
            $table->text('content')->nullable();
            $table->string('name_type')->nullable();
            $table->string('width')->nullable();
            $table->string('longitude')->nullable();
            $table->integer('available_installments')->nullable();
            $table->integer('found_cheaper')->nullable();
            $table->integer('conclusion_contract')->nullable();
            $table->integer('state_compensation')->nullable();
            $table->integer('status')->default(1);
            $table->string('next_to')->nullable();
            $table->string('underground')->nullable();
            $table->double('rating', 8, 2)->nullable();
            $table->text('cemetery_ids')->nullable();
            $table->string('role')->default('organization');
            $table->text('awards')->nullable();
            $table->string('price_list')->nullable();
            $table->integer('priority')->nullable()->default(0);
            $table->integer('rotation_order')->nullable()->default(0);
            $table->string('remains')->nullable();
            $table->text('slug');
            $table->integer('href_img')->default(0);
            $table->integer('href_main_img')->nullable()->default(0);
            $table->text('whatsapp')->nullable();
            $table->string('telegram')->nullable();
            $table->string('email')->nullable();
            $table->string('village')->nullable();
            $table->text('two_gis_link')->nullable();
            $table->integer('time_difference')->default(0);
            $table->text('comment_admin')->nullable();
            $table->string('calls')->nullable();
            $table->bigInteger('inn')->nullable();
            $table->string('type_phone')->nullable();
            $table->text('responsible_organization')->nullable();
            $table->text('address_responsible_person')->nullable();
            $table->text('responsible_person_full_name')->nullable();
            $table->text('okved')->nullable();
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
