<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->text('description');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Временно делаем без foreign key, добавим позже
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('priority_id');
            $table->unsignedBigInteger('status_id');
            
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        // Добавляем foreign keys отдельно после создания всех таблиц
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('ticket_categories');
            $table->foreign('priority_id')->references('id')->on('ticket_priorities');
            $table->foreign('status_id')->references('id')->on('ticket_statuses');
        });
    }

    public function down()
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['priority_id']);
            $table->dropForeign(['status_id']);
        });
        
        Schema::dropIfExists('support_tickets');
    }
};