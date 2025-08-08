<div>
    <p>Экспорт завершен. Обработано {{ $export->successful_rows }} строк.</p>
    <a href="{{ $downloadUrl }}" 
       class="text-primary-500 underline hover:text-primary-600"
       download>
        Скачать файл
    </a>
    <p class="text-xs text-gray-500 mt-1">
        Файл: {{ basename($export->file_name) }}
    </p>
</div>