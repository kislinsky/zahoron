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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('parent_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->string('surname')->nullable();
            $table->string('patronymic')->nullable();
            $table->string('phone')->nullable();
            $table->text('adres')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('telegram')->nullable();
            $table->string('role')->default('user');
            $table->string('theme')->default('light');
            $table->text('language')->nullable();
            $table->integer('sms_notifications')->default(1);
            $table->integer('email_notifications')->default(1);
            $table->string('inn')->nullable();
            $table->text('uploading_signature')->nullable();
            $table->string('number_cart')->nullable();
            $table->string('bank')->nullable();
            $table->text('cemetery_ids')->nullable();
            $table->json('city_ids')->nullable();
            $table->string('ogrn')->nullable();
            $table->text('ogrnip')->nullable();
            $table->text('kpp')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->bigInteger('organization_id_branch')->nullable();
            $table->string('organizational_form')->default('ep');
            $table->string('name_organization', 355)->nullable();
            $table->unsignedBigInteger('edge_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->text('in_face')->nullable();
            $table->text('regulation')->nullable();
            $table->integer('status')->default(1);
            $table->integer('app_organization')->default(0);
            $table->text('acting_basis_of')->nullable();
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
