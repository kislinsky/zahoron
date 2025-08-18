<?php 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('call_stats', function (Blueprint $table) {
            // Основные поля
            $table->id();
            $table->unsignedBigInteger('organization_id')->nullable();
            
            // Параметры коллтрекинга
            $table->string('uid')->nullable()->comment('Уникальный идентификатор клиента MANGO OFFICE');
            $table->string('ga_cid')->nullable()->comment('Идентификатор клиента Google Analytics');
            $table->string('ya_cid')->nullable()->comment('Идентификатор клиента Яндекс Метрики');
            $table->string('rs_cid')->nullable()->comment('Идентификатор сеанса Roistat');
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable()->comment('Канал');
            $table->string('utm_campaign')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('utm_term')->nullable();
            
            // Гео данные
            $table->string('country_code', 2)->nullable()->comment('Код ISO страны');
            $table->string('region_code', 10)->nullable()->comment('Код ISO региона');
            $table->string('city')->nullable();
            
            // Данные устройства
            $table->string('device')->nullable()->comment('desktop, tablet или mobile');
            $table->string('ip')->nullable();
            
            // URL данные
            $table->text('url')->nullable()->comment('Страница, с которой совершён звонок');
            $table->text('first_url')->nullable()->comment('Страница входа');
            $table->text('custom_params')->nullable()->comment('Дополнительные параметры');
            
            // Флаги
            $table->boolean('is_duplicate')->default(false)->comment('Флаг повторного обращения');
            $table->boolean('is_quality')->default(false)->comment('Флаг качественного обращения');
            $table->boolean('is_new')->default(false)->comment('Флаг уникального обращения');
            
            // Данные звонка
            $table->string('call_id')->unique()->comment('ID звонка');
            $table->string('webhook_type')->nullable();
            $table->string('last_group')->nullable()->comment('Наименование группы');
            $table->text('record_url')->nullable()->comment('Ссылка на запись');
            $table->dateTime('date_start')->nullable()->comment('Время поступления');
            $table->string('caller_number')->nullable()->comment('Номер звонившего');
            $table->string('call_type')->nullable();
            $table->dateTime('date_end')->nullable()->comment('Время окончания');
            $table->string('call_status')->nullable()->comment('Статус завершения');
            $table->integer('duration')->nullable()->comment('Продолжительность в секундах');
            $table->string('number_hash')->nullable()->comment('Хеш номера');
            $table->integer('wait_time')->nullable()->comment('Время ожидания в секундах');
            $table->string('called_number')->nullable()->comment('Номер, на который принят звонок');
            
            $table->timestamps();
            
            // Индексы
            $table->index('organization_id');
            $table->index('caller_number');
            $table->index('date_start');
        });
    }

    public function down()
    {
        Schema::dropIfExists('call_stats');
    }
};