{{-- resources/views/filament/widgets/quick-actions.blade.php --}}
<div class="p-6 bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Быстрые действия</h3>
        <x-filament::icon-button
            icon="heroicon-o-ellipsis-horizontal"
            size="sm"
            color="gray"
        />
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($actions as $action)
            <a 
                href="{{ $action['url'] }}" 
                class="group flex flex-col items-center justify-center p-4 text-center rounded-lg border border-gray-200 hover:border-{{ $action['color'] }}-300 hover:bg-{{ $action['color'] }}-50 transition-all duration-200 hover:shadow-sm"
            >
                <div class="p-3 rounded-full bg-{{ $action['color'] }}-100 text-{{ $action['color'] }}-600 group-hover:bg-{{ $action['color'] }}-200 mb-3 transition-colors">
                    @svg($action['icon'], 'w-6 h-6')
                </div>
                <span class="text-sm font-medium text-gray-700 group-hover:text-{{ $action['color'] }}-700">
                    {{ $action['label'] }}
                </span>
            </a>
        @endforeach
    </div>
</div>