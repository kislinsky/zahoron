<?php

namespace App\Filament\Resources\OrganizationResource\RelationManagers;

use App\Models\CallStat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CallsRelationManager extends RelationManager
{
    protected static string $relationship = 'calls';

    protected static ?string $title = 'Звонки';

    protected static ?string $label = 'звонок';

    protected static ?string $pluralLabel = 'Звонки';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('call_id')
                            ->label('ID звонка')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('caller_number')
                            ->label('Номер звонящего')
                            ->tel()
                            ->maxLength(20),
                        
                        Forms\Components\TextInput::make('called_number')
                            ->label('Номер назначения')
                            ->tel()
                            ->maxLength(20),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Детали звонка')
                    ->schema([
                        Forms\Components\Select::make('call_status')
                            ->label('Статус')
                            ->options([
                                '1100' => 'Принят (1100)',
                                '1101' => 'Принят (1101)', 
                                '1110' => 'Принят (1110)',
                                '1111' => 'Принят (1111)',
                                '400' => 'Отклонен (400)',
                                '404' => 'Отклонен (404)',
                                '486' => 'Отклонен (486)',
                            ]),
                        
                        Forms\Components\Select::make('call_type')
                            ->label('Тип звонка')
                            ->options([
                                '1' => 'Динамический',
                                '2' => 'Статический',
                                '3' => 'Дефолтный',
                            ]),
                        
                        Forms\Components\TextInput::make('duration')
                            ->label('Длительность (сек)')
                            ->numeric(),
                        
                        Forms\Components\DateTimePicker::make('date_start')
                            ->label('Время начала'),
                        
                        Forms\Components\DateTimePicker::make('date_end')
                            ->label('Время окончания'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('UTM параметры')
                    ->schema([
                        Forms\Components\TextInput::make('utm_source')
                            ->label('UTM Source')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('utm_medium')
                            ->label('UTM Medium')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('utm_campaign')
                            ->label('UTM Campaign')
                            ->maxLength(255),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Геоданные')
                    ->schema([
                        Forms\Components\TextInput::make('city')
                            ->label('Город')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('country_code')
                            ->label('Код страны')
                            ->maxLength(2),
                        
                        Forms\Components\TextInput::make('region_code')
                            ->label('Код региона')
                            ->maxLength(10),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Флаги')
                    ->schema([
                        Forms\Components\Toggle::make('is_quality')
                            ->label('Качественное'),
                        
                        Forms\Components\Toggle::make('is_duplicate')
                            ->label('Дубликат'),
                        
                        Forms\Components\Toggle::make('is_new')
                            ->label('Новое'),
                    ])
                    ->columns(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('call_id')
            ->columns([
                Tables\Columns\TextColumn::make('call_id')
                    ->label('ID звонка')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('caller_number')
                    ->label('Номер звонящего')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('called_number')
                    ->label('Номер назначения')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('call_status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn (string $state): string => match (substr($state, 0, 2)) {
                        '11' => 'success',
                        default => 'danger'
                    })
                    ->formatStateUsing(fn (string $state): string => match (substr($state, 0, 2)) {
                        '11' => 'Принят (' . $state . ')',
                        default => 'Отклонен (' . $state . ')'
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('duration')
                    ->label('Длительность')
                    ->formatStateUsing(fn ($state) => $state ? gmdate('H:i:s', $state) : '00:00:00')
                    ->sortable(),

                Tables\Columns\TextColumn::make('date_start')
                    ->label('Время начала')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('city')
                    ->label('Город')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('utm_source')
                    ->label('UTM Source')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_quality')
                    ->label('Качество')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_duplicate')
                    ->label('Дубликат')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('call_type')
                    ->label('Тип звонка')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        '1' => 'Динамический',
                        '2' => 'Статический',
                        '3' => 'Дефолтный',
                        default => 'Неизвестно'
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('call_status')
                    ->label('Статус звонка')
                    ->options([
                        '1100' => 'Принят (1100)',
                        '1101' => 'Принят (1101)',
                        '1110' => 'Принят (1110)',
                        '1111' => 'Принят (1111)',
                        '400' => 'Отклонен (400)',
                        '404' => 'Отклонен (404)',
                        '486' => 'Отклонен (486)',
                    ]),

                Tables\Filters\SelectFilter::make('call_type')
                    ->label('Тип звонка')
                    ->options([
                        '1' => 'Динамический',
                        '2' => 'Статический',
                        '3' => 'Дефолтный',
                    ]),

                Tables\Filters\SelectFilter::make('status_group')
                    ->label('Группа статусов')
                    ->options([
                        'accepted' => 'Принятые звонки',
                        'rejected' => 'Отклоненные звонки',
                        'no_status' => 'Без статуса',
                    ])
                    ->query(function (Builder $query, $data) {
                        $value = $data['value'];
                        
                        return match ($value) {
                            'accepted' => $query->where('call_status', 'like', '11%'),
                            'rejected' => $query->whereNotNull('call_status')
                                             ->whereNot('call_status', 'like', '11%'),
                            'no_status' => $query->whereNull('call_status'),
                            default => $query,
                        };
                    }),

                Tables\Filters\Filter::make('date_range')
                    ->label('Диапазон дат')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('С даты'),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('По дату'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_start', '>=', $date),
                            )
                            ->when(
                                $data['end_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_start', '<=', $date),
                            );
                    }),

                Tables\Filters\TernaryFilter::make('is_quality')
                    ->label('Качественные звонки'),

                Tables\Filters\TernaryFilter::make('is_duplicate')
                    ->label('Дубликаты'),

                Tables\Filters\SelectFilter::make('city')
                    ->label('Город')
                    ->searchable()
                    ->options(function () {
                        return CallStat::where('organization_id', $this->getOwnerRecord()->id)
                            ->whereNotNull('city')
                            ->distinct('city')
                            ->pluck('city', 'city')
                            ->toArray();
                    }),
            ])
            ->headerActions([
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date_start', 'desc');
    }

    public static function getDescription(): string
    {
        return 'Список звонков, связанных с этой организацией';
    }
}