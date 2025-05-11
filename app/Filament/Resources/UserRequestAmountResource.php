<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\UserRequestAmount;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserRequestAmountResource\Pages;
use App\Filament\Resources\UserRequestAmountResource\RelationManagers;

class UserRequestAmountResource extends Resource
{
    protected static ?string $model = UserRequestAmount::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Купленные заявки'; // Название в меню
    protected static ?string $navigationGroup = 'Заявки'; // Указываем группу

    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('organization_id')
                ->label('Организация')
                ->relationship('organization', 'title') // Предполагается, что есть отношение 'organization'
                ->required(),

            Forms\Components\Select::make('type_service_id')
                ->label('Тип услуги')
                ->relationship('typeService', 'title_ru') // Предполагается, что есть отношение 'typeService'
                ->required(),

            Forms\Components\Select::make('type_application_id')
                ->label('Тип заявки')
                ->relationship('typeApplication', 'title_ru') // Предполагается, что есть отношение 'typeApplication'
                ->required(),

            Forms\Components\TextInput::make('price')
                ->label('Цена')
                ->required()
                ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                ->label('id')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('typeApplication.title_ru')
                ->label('Тип заявки')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('typeService.title_ru')
                ->label('Тип услуги')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('organization_id')
                ->label('id органиазции')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('price')
                ->label('Сумма')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('created_at') // Добавляем столбец с датой создания
                ->label('Дата создания')
                ->dateTime('d.m.Y H:i:s') // Форматируем дату (день.месяц.год часы:минуты:секунды)
                ->searchable()
                ->sortable(),
            ])
            ->filters([
                Filter::make('today')
                ->label('За сегодня')
                ->query(fn (Builder $query) => $query->whereDate('created_at', today())),

            // Фильтр "За вчера"
            Filter::make('yesterday')
                ->label('За вчера')
                ->query(fn (Builder $query) => $query->whereDate('created_at', today()->subDay())),

            // Фильтр "За неделю"
            Filter::make('last_week')
                ->label('За неделю')
                ->query(fn (Builder $query) => $query->whereBetween('created_at', [now()->subWeek(), now()])),

            // Фильтр "За месяц"
            Filter::make('last_month')
                ->label('За месяц')
                ->query(fn (Builder $query) => $query->whereBetween('created_at', [now()->subMonth(), now()])),

            // Фильтр "Выбрать период"
            Filter::make('date_range')
            ->form([
                DatePicker::make('start_date')
                    ->label('Начальная дата'),
                DatePicker::make('end_date')
                    ->label('Конечная дата'),
            ])
            ->query(function (Builder $query, array $data) {
                return $query
                    ->when(
                        $data['start_date'],
                        fn (Builder $query, $startDate) => $query->whereDate('created_at', '>=', $startDate)
                    )
                    ->when(
                        $data['end_date'],
                        fn (Builder $query, $endDate) => $query->whereDate('created_at', '<=', $endDate)
                    );
            })
            ->indicateUsing(function (array $data): ?string {
                if (!$data['start_date'] || !$data['end_date']) {
                    return null;
                }
                return 'Диапазон дат: ' . $data['start_date'] . ' - ' . $data['end_date'];
            }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserRequestAmounts::route('/'),
            'create' => Pages\CreateUserRequestAmount::route('/create'),
            'edit' => Pages\EditUserRequestAmount::route('/{record}/edit'),
        ];
    }
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'admin' ;
    }

    public static function canViewAny(): bool
    {
        return static::shouldRegisterNavigation();
    }
}
