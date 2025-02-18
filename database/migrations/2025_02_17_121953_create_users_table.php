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
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name', 255)->nullable();
        $table->string('email', 255)->nullable();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password', 255);
        $table->rememberToken();
        $table->timestamps();
        $table->string('surname', 255)->nullable();
        $table->string('patronymic', 255)->nullable();
        $table->string('phone', 255)->nullable();
        $table->text('adres')->nullable();
        $table->string('whatsapp', 255)->nullable();
        $table->string('telegram', 255)->nullable();
        $table->string('role', 255)->default('user');
        $table->string('theme', 255)->default('light');
        $table->text('language')->nullable();
        $table->integer('sms_notifications')->default(1);
        $table->integer('email_notifications')->default(1);
        $table->string('inn', 255)->nullable();
        $table->text('uploading_signature')->nullable();
        $table->string('number_cart', 255)->nullable();
        $table->string('bank', 255)->nullable();
        $table->text('cemetery_ids')->nullable();
        $table->string('ogrn', 255)->nullable();
        $table->string('icon', 255)->nullable();
        $table->unsignedBigInteger('organization_id')->nullable();
        $table->string('organizational_form', 255)->default('ep');
        $table->string('name_organization', 355)->nullable();
        $table->unsignedBigInteger('edge_id')->nullable();
        $table->unsignedBigInteger('city_id')->nullable();
        $table->text('in_face')->nullable();
        $table->text('regulation')->nullable();
        $table->integer('status')->default(1);

        // $table->foreign('edge_id')->references('id')->on('edges');
        // $table->foreign('city_id')->references('id')->on('cities');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
