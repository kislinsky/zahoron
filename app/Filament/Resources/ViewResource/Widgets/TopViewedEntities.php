<?php

namespace App\Filament\Resources\ViewResource\Widgets;

use App\Models\View;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TopViewedEntities extends BaseWidget
{
    protected static ?string $heading = 'Топ просматриваемых объектов';

    protected int | string | array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return View::query()
            ->select(
                'entity_type', 
                'entity_id', 
                DB::raw('COUNT(*) as view_count'),
                DB::raw('CONCAT(entity_type, "-", entity_id) as unique_key')
            )
            ->groupBy('entity_type', 'entity_id')
            ->orderBy('view_count', 'desc')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('entity_type')
                ->label('Тип объекта')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'cemetery' => 'success',
                    'mortuary' => 'warning',
                    'organization' => 'info',
                    'page' => 'primary',
                    default => 'gray',
                })
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'cemetery' => 'Кладбище',
                    'mortuary' => 'Морг',
                    'organization' => 'Организация',
                    'page' => 'Страница',
                    default => $state,
                }),

            Tables\Columns\TextColumn::make('entity_id')
                ->label('ID объекта'),

            Tables\Columns\TextColumn::make('view_count')
                ->label('Количество просмотров')
                ->sortable()
                ->color('primary'),
        ];
    }

    public function getTableRecordKey($record): string
    {
        // Создаем уникальный ключ из типа и ID объекта
        return $record->entity_type . '-' . $record->entity_id;
    }
}