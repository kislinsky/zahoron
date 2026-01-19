<div class="space-y-2 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
        @foreach($variables as $variable => $description)
            <div class="flex items-start space-x-2 p-2 bg-white dark:bg-gray-700 rounded border dark:border-gray-600">
                <code class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-sm font-mono whitespace-nowrap">
                    {{ $variable }}
                </code>
                <code class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-sm font-mono whitespace-nowrap">
                    {{ $description }}
                </code>
            </div>
        @endforeach
    </div>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
        <strong class="dark:text-gray-300">Как использовать:</strong> Просто вставьте переменную в текст ответа, например: "Наш адрес: {adress}, телефон: {phone}"
    </p>
</div>