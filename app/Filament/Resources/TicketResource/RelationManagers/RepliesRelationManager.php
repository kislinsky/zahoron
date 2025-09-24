<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RepliesRelationManager extends RelationManager
{
    protected static string $relationship = 'replies';

    protected static ?string $title = 'История переписки';

    protected static ?string $recordTitleAttribute = 'message';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Пользователь')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->default(auth()->id()),
                
                Forms\Components\Textarea::make('message')
                    ->label('Сообщение')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),
                
                Forms\Components\Toggle::make('is_internal')
                    ->label('Внутренний комментарий')
                    ->helperText('Виден только сотрудникам поддержки')
                    ->default(false),
                
                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Дата создания')
                    ->displayFormat('d.m.Y H:i')
                    ->disabled(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Автор')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('message')
                    ->label('Сообщение')
                    ->limit(100)
                    ->html()
                    ->formatStateUsing(fn ($state) => nl2br(e($state))),
                
                Tables\Columns\IconColumn::make('is_internal')
                    ->label('Внутренний')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-chat-bubble-left'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('is_internal')
                    ->label('Тип сообщения')
                    ->options([
                        '0' => 'Публичные',
                        '1' => 'Внутренние',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить ответ')
                    ->icon('heroicon-o-plus'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil'),
                
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}