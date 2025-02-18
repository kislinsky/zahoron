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
        Schema::create('activity_category_organizations', function (Blueprint $table) {
            $table->id(); // bigint UNSIGNED AUTO_INCREMENT (Первичный ключ)
            $table->timestamps(); // created_at, updated_at

            // Внешний ключ на organizations
            $table->foreignId('organization_id')
                ->constrained('organizations')
                ->cascadeOnDelete(); // Удалит записи при удалении связанной organization

                $table->text('category_main_id')->nullable();
                $table->text('category_children_id')->nullable();

            // // Категории (если есть таблица \categories\, можно привязать как внешний ключ)
            // $table->foreignId('category_main_id')->constrained('category_products')->cascadeOnDelete();
            // $table->foreignId('category_children_id')->constrained('category_products')->cascadeOnDelete();

            $table->integer('price')->nullable(); // Цена (может быть NULL)
            
            // Изменил float на decimal(5,2), чтобы избежать проблем с точностью
            $table->decimal('rating', 5, 2)->nullable();

            // Поля, которые могут хранить JSON-данные (если в будущем планируется работа с JSON, рекомендуется \json\)
            $table->text('sales')->nullable();
            $table->text('cemetery_ids')->nullable();
            $table->text('district_ids')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_category_organizations');
    }
};
