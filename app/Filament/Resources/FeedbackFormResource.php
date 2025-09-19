<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedbackFormResource\Pages;
use App\Filament\Resources\FeedbackFormResource\RelationManagers;
use App\Models\FeedbackForm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FeedbackFormResource extends Resource
{
    protected static ?string $model = FeedbackForm::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Заявки обратной связи';

    protected static ?string $modelLabel = 'заявка';

    protected static ?string $pluralModelLabel = 'Заявки обратной связи';

    protected static ?string $navigationGroup = 'Обратная связь';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Информация о заявке')
                    ->schema([
                        Forms\Components\TextInput::make('topic')
                            ->label('Тема вопроса')
                            ->required()
                            ->maxLength(255)
                            ->disabled(),

                        Forms\Components\Textarea::make('question')
                            ->label('Вопрос')
                            ->required()
                            ->rows(5)
                            ->maxLength(1000)
                            ->columnSpanFull()
                            ->disabled(),

                        Forms\Components\TextInput::make('name')
                            ->label('Имя')
                            ->required()
                            ->maxLength(255)
                            ->disabled(),

                        Forms\Components\TextInput::make('phone')
                            ->label('Телефон')
                            ->required()
                            ->maxLength(20)
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Дата создания')
                            ->displayFormat('d.m.Y H:i')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('updated_at')
                            ->label('Дата обновления')
                            ->displayFormat('d.m.Y H:i')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('topic')
                    ->label('Тема')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('question')
                    ->label('Вопрос')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Имя')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Дата обновления')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('topic')
                    ->label('Тема вопроса')
                    ->options([
                        'Поиск могил' => 'Поиск могил',
                    ]),

                Tables\Filters\Filter::make('created_at')
                    ->label('Дата создания')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('С'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('По'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers здесь при необходимости
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeedbackForms::route('/'),
            'create' => Pages\CreateFeedbackForm::route('/create'),
            'view' => Pages\ViewFeedbackForm::route('/{record}'),
            'edit' => Pages\EditFeedbackForm::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest();
    }
}