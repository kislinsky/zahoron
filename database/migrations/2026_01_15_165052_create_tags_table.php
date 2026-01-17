<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            
            // Тип привязки (категория, товар, услуга, организация)
            $table->string('entity_type', 50)->index(); 
            // Пример: 'category_product', 'product', 'service', 'organization'
            
            // ID сущности к которой привязан тег
            $table->unsignedBigInteger('entity_id')->index();
            
            // Сам тег/хэштег (хранится отдельно каждый)
            $table->string('name', 191)->index(); 
            // Пример: 'памятники из гранита', 'Кресты на могилу из металла'
            
            // Slug для URL
            $table->string('slug', 191)->index();
            
            // Приоритет тега (для сортировки)
            $table->tinyInteger('priority')->default(0)->index();
            
            // Тип тега (для группировки)
            $table->string('tag_type', 50)->nullable()->index();
            // Пример: 'material', 'style', 'filter', 'popular', 'related'
            
            // SEO поля
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            
            // Статистика
            $table->unsignedInteger('search_count')->default(0)->index(); // сколько раз искали
            $table->unsignedInteger('click_count')->default(0)->index(); // сколько раз кликали
            $table->unsignedInteger('product_count')->default(0)->index(); // сколько товаров с таким тегом
            
            // Активность
            $table->boolean('is_active')->default(true)->index();
            
            // Создано/обновлено
            $table->timestamps();
            
            // Уникальный индекс: тег + сущность
            $table->unique(['entity_type', 'entity_id', 'name'], 'unique_tag_entity');
            
            // Составные индексы для быстрого поиска
            $table->index(['entity_type', 'entity_id', 'priority']);
            $table->index(['entity_type', 'entity_id', 'tag_type']);
            $table->index(['name', 'entity_type', 'is_active']);
            $table->index(['tag_type', 'search_count']);
            $table->index(['entity_type', 'search_count']);
            
        
        });
        
       
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};