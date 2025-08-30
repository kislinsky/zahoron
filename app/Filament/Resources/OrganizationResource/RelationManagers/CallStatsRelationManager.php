<?php

namespace App\Filament\Resources\OrganizationResource\RelationManagers;

use App\Models\CallStat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CallStatsRelationManager extends RelationManager
{
    protected static string $relationship = 'calls';
    protected static ?string $title = 'Звонки';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('call_id')
                    ->label('ID звонка'),
                Forms\Components\TextInput::make('uid')
                    ->label('UID'),
                Forms\Components\TextInput::make('caller_number')
                    ->label('Номер звонящего')
                    ->tel(),
                Forms\Components\TextInput::make('called_number')
                    ->label('Номер назначения')
                    ->tel(),
                Forms\Components\DateTimePicker::make('date_start')
                    ->label('Начало звонка'),
                Forms\Components\DateTimePicker::make('date_end')
                    ->label('Конец звонка'),
                Forms\Components\TextInput::make('duration')
                    ->label('Длительность (сек)')
                    ->numeric(),
                Forms\Components\TextInput::make('wait_time')
                    ->label('Время ожидания (сек)')
                    ->numeric(),
                Forms\Components\Select::make('call_status')
                    ->label('Статус звонка')
                    ->options([
                        'answered' => 'Отвечен',
                        'no_answer' => 'Не отвечен',
                        'busy' => 'Занято',
                        'failed' => 'Неудачный',
                    ]),
                Forms\Components\Select::make('call_type')
                    ->label('Тип звонка')
                    ->options([
                        'incoming' => 'Входящий',
                        'outgoing' => 'Исходящий',
                    ]),
                Forms\Components\Toggle::make('is_quality')
                    ->label('Качественный'),
                Forms\Components\Toggle::make('is_new')
                    ->label('Новый клиент'),
                Forms\Components\Toggle::make('is_duplicate')
                    ->label('Дубликат'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('call_id')
            ->columns([
                Tables\Columns\TextColumn::make('call_id')
                    ->label('ID звонка')
                    ->searchable(),
                Tables\Columns\TextColumn::make('caller_number')
                    ->label('Номер звонящего')
                    ->searchable(),
                Tables\Columns\TextColumn::make('called_number')
                    ->label('Номер назначения')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_start')
                    ->label('Начало')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->label('Длительность')
                    ->formatStateUsing(fn ($state) => $state ? gmdate('H:i:s', $state) : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('call_status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'answered' => 'Отвечен',
                        'no_answer' => 'Не отвечен',
                        'busy' => 'Занято',
                        'failed' => 'Неудачный',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'answered' => 'success',
                        'no_answer' => 'warning',
                        'busy' => 'gray',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('call_type')
                    ->label('Тип')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'incoming' => 'Входящий',
                        'outgoing' => 'Исходящий',
                        default => $state,
                    }),
                Tables\Columns\IconColumn::make('is_quality')
                    ->label('Качество')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_new')
                    ->label('Новый')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('call_status')
                    ->label('Статус звонка')
                    ->options([
                        'answered' => 'Отвечен',
                        'no_answer' => 'Не отвечен',
                        'busy' => 'Занято',
                        'failed' => 'Неудачный',
                    ]),
                Tables\Filters\SelectFilter::make('call_type')
                    ->label('Тип звонка')
                    ->options([
                        'incoming' => 'Входящий',
                        'outgoing' => 'Исходящий',
                    ]),
                Tables\Filters\TernaryFilter::make('is_quality')
                    ->label('Качественный звонок'),
                Tables\Filters\TernaryFilter::make('is_new')
                    ->label('Новый клиент'),
                Tables\Filters\Filter::make('date_start')
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
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->form([
                        Forms\Components\TextInput::make('uid')
                            ->label('Уникальный идентификатор клиента MANGO OFFICE'),
                        Forms\Components\TextInput::make('utm_term')
                            ->label('Ключевое слово'),
                        Forms\Components\Select::make('device')
                            ->label('Тип устройства')
                            ->options([
                                'desktop' => 'Desktop',
                                'tablet' => 'Tablet',
                                'mobile' => 'Mobile',
                            ]),
                        Forms\Components\TextInput::make('url')
                            ->label('Адрес страницы сайта')
                            ->url(),
                        
                        Forms\Components\TextInput::make('call_id')
                            ->label('Id звонка'),
                        Forms\Components\TextInput::make('caller_number')
                            ->label('Номер звонившего'),
                        Forms\Components\TextInput::make('called_number')
                            ->label('Номер, на который был принят звонок'),
                        Forms\Components\TextInput::make('ya_cid')
                            ->label('Идентификатор клиента Яндекс Метрики'),
                        Forms\Components\TextInput::make('ip')
                            ->label('IP адрес пользователя'),
                        Forms\Components\TextInput::make('record_url')
                            ->label('Ссылка на запись звонка')
                            ->url(),
                        Forms\Components\Select::make('call_type')
                            ->label('Тип звонка')
                            ->options([
                                'incoming' => 'Входящий',
                                'outgoing' => 'Исходящий',
                            ]),
                        Forms\Components\DateTimePicker::make('date_end')
                            ->label('Время окончания звонка'),
                        Forms\Components\DateTimePicker::make('date_start')
                            ->label('Время поступления звонка'),
                        Forms\Components\TextInput::make('duration')
                            ->label('Продолжительность звонка (сек)')
                            ->numeric(),
                      
                        Forms\Components\Toggle::make('is_quality')
                            ->label('Качественный звонок'),
                        Forms\Components\Toggle::make('is_new')
                            ->label('Новый клиент'),
                        Forms\Components\Toggle::make('is_duplicate')
                            ->label('Дубликат'),
                    ]),
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
}