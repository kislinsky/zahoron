@php
use Illuminate\Support\Facades\Storage;

    $record = $getRecord();
@endphp

<div class="space-y-4">
    <div class="text-sm font-medium text-gray-700">Загруженные фотографии</div>
    
    @if(!$record || empty($record->image_attachments))
        <p class="text-gray-500 text-sm py-2">Нет загруженных фотографий</p>
    @else
        @php
            $attachments = json_decode($record->image_attachments);
        @endphp
        
        @if(empty($attachments))
            <p class="text-gray-500 text-sm py-2">Нет загруженных фотографий</p>
        @else
            <div class="text-sm text-gray-600 mb-3">Количество: {{ count($attachments) }}</div>
            <div class="grid grid-cols-4 gap-3">
                @foreach($attachments as $attachment)
                    @if(isset($attachment->path))
                        @php
                            $url = Storage::url($attachment->path);
                            $filename = $attachment->original_name ?? $attachment->filename ?? 'Фото';
                        @endphp
                        <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                            <a href="{{ $url }}" target="_blank" class="block">
                                <img src="{{ $url }}" 
                                     alt="{{ $filename }}" 
                                     class="w-full h-24 object-cover hover:opacity-90 transition-opacity">
                            </a>
                            <div class="p-2 bg-gray-50">
                                <div class="text-xs font-medium text-gray-700 truncate">{{ $filename }}</div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    @endif
</div>