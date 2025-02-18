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
        Schema::create('comment_products', function (Blueprint $table) {
            $table->id(); // bigInt UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->timestamps(); // created_at, updated_at

            $table->text('content')->collation('utf8mb4_unicode_ci'); // Текст комментария

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete(); // Если продукт удаляется, удаляются и его комментарии

            $table->string('name', 255)->collation('utf8mb4_unicode_ci'); // Имя автора
            $table->string('surname', 255)->collation('utf8mb4_unicode_ci'); // Фамилия автора

            $table->integer('status')->default(0); // Статус комментария, по умолчанию 0

            $table->foreignId('category_id')
                ->constrained('category_products')
                ->cascadeOnDelete(); // Удаление всех комментариев к удаленной категории

            $table->foreignId('organization_id')
                ->constrained('organizations')
                ->cascadeOnDelete(); // Удаление всех комментариев, связанных с удаленной организацией

            $table->text('organization_response')
                ->nullable()
                ->collation('utf8mb4_unicode_ci'); // Ответ организации (может быть NULL)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_products');
    }
};
