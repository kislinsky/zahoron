<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CallStatResource\Pages;
use App\Models\CallStat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CallStatResource extends Resource
{
    protected static ?string $model = CallStat::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';

    protected static ?string $navigationGroup = 'Статистика';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                 Forms\Components\TextInput::make('id')
                            ->numeric()
                            ->label('ID'),

                        Forms\Components\TextInput::make('organization_id')
                            ->numeric()
                            ->label('ID организации'),
                        
                        Forms\Components\TextInput::make('uid')
                            ->label('UID клиента')
                            ->maxLength(255),
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
                        
                        Forms\Components\TextInput::make('utm_content')
                            ->label('UTM Content')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('utm_term')
                            ->label('UTM Term')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Аналитика')
                    ->schema([
                        Forms\Components\TextInput::make('ga_cid')
                            ->label('Google Analytics CID')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('ya_cid')
                            ->label('Yandex Metrika CID')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('rs_cid')
                            ->label('Roistat CID')
                            ->maxLength(255),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Геоданные')
                    ->schema([
                        Forms\Components\TextInput::make('country_code')
                            ->label('Код страны')
                            ->maxLength(2),
                        
                        Forms\Components\TextInput::make('region_code')
                            ->label('Код региона')
                            ->maxLength(10),
                        
                        Forms\Components\TextInput::make('city')
                            ->label('Город')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('ip')
                            ->label('IP адрес')
                            ->maxLength(45),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Данные устройства')
                    ->schema([
                        Forms\Components\Select::make('device')
                            ->label('Устройство')
                            ->options([
                                'desktop' => 'Desktop',
                                'tablet' => 'Tablet',
                                'mobile' => 'Mobile',
                            ]),
                    ]),

                Forms\Components\Section::make('URL данные')
                    ->schema([
                        Forms\Components\Textarea::make('url')
                            ->label('URL страницы')
                            ->rows(2),
                        
                        Forms\Components\Textarea::make('first_url')
                            ->label('URL входа')
                            ->rows(2),
                        
                        Forms\Components\Textarea::make('custom_params')
                            ->label('Дополнительные параметры')
                            ->rows(3),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Флаги')
                    ->schema([
                        Forms\Components\Toggle::make('is_duplicate')
                            ->label('Дубликат')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_quality')
                            ->label('Качественное')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_new')
                            ->label('Новое')
                            ->default(false),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Данные звонка')
                    ->schema([
                        Forms\Components\TextInput::make('call_id')
                            ->label('ID звонка')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('webhook_type')
                            ->label('Тип вебхука')
                            ->options([
                                // Добавьте возможные типы вебхуков
                            ]),
                        
                        Forms\Components\TextInput::make('last_group')
                            ->label('Группа')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('record_url')
                            ->label('Запись звонка')
                            ->url()
                            ->maxLength(500),
                        
                        Forms\Components\DateTimePicker::make('date_start')
                            ->label('Время начала'),
                        
                        Forms\Components\TextInput::make('caller_number')
                            ->label('Номер звонящего')
                            ->tel()
                            ->maxLength(20),
                        
                        Forms\Components\Select::make('call_type')
                            ->label('Тип звонка')
                            ->options([
                                '1' => 'динамический',
                                '2' => 'статический',
                                '3' => 'дефолтный',
                            ]),
                        
                        Forms\Components\DateTimePicker::make('date_end')
                            ->label('Время окончания'),
                        
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
        // добавьте другие нужные статусы
    ])
    ->default(''),
                        
                        Forms\Components\TextInput::make('duration')
                            ->label('Длительность (сек)')
                            ->numeric(),
                        
                        Forms\Components\TextInput::make('number_hash')
                            ->label('Хеш номера')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('wait_time')
                            ->label('Время ожидания (сек)')
                            ->numeric(),
                        
                        Forms\Components\TextInput::make('called_number')
                            ->label('Номер назначения')
                            ->tel()
                            ->maxLength(20),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
         Tables\Columns\TextColumn::make('id')
                    ->label('ID ')
                    ->searchable()
                    ->sortable(),


                Tables\Columns\TextColumn::make('call_id')
                    ->label('ID звонка')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('caller_number')
                    ->label('Номер звонящего')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('called_number')
                    ->label('Номер на который звонят')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('device')
                    ->label('Устройство')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('ip')
                    ->label('ip')
                    ->searchable()
                    ->sortable(),


            ])
            ->filters([
                Tables\Filters\SelectFilter::make('call_type')
                    ->label('Тип звонка')
                    ->options([
                         '1' => 'динамический',
                                '2' => 'статический',
                                '3' => 'дефолтный',
                    ]),

                Tables\Filters\SelectFilter::make('call_status')
                    ->label('Статус')
                     ->options([
        '11' => 'Принятые звонки (11XX)',
        'other' => 'Отклоненные звонки',
        '' => 'Нет данных',
    ])
    ->query(function (Builder $query, $data) {
        $value = $data['value'];
        
        return match ($value) {
            '11' => $query->where('call_status', 'like', '11%'),
            'other' => $query->whereNot('call_status', 'like', '11%')
                             ->whereNotNull('call_status'),
            '' => $query->whereNull('call_status'),
            default => $query,
        };
    }),

                Tables\Filters\Filter::make('date_start')
                    ->form([
                        Forms\Components\DatePicker::make('start_date'),
                        Forms\Components\DatePicker::make('end_date'),
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

                Tables\Filters\TernaryFilter::make('is_new')
                    ->label('Новые звонки'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date_start', 'desc');
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
            'index' => Pages\ListCallStats::route('/'),
            'create' => Pages\CreateCallStat::route('/create'),
            'view' => Pages\ViewCallStat::route('/{record}'),
            'edit' => Pages\EditCallStat::route('/{record}/edit'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Статистика звонков';
    }

    public static function getPluralLabel(): string
    {
        return 'Статистика звонков';
    }

    public static function getModelLabel(): string
    {
        return 'запись звонка';
    }
}