<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class UserRequestCountRelationManager extends RelationManager
{
    protected static string $relationship = 'userRequestCount';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type_application_id')
                    ->label('Тип заявки')
                    ->relationship('typeApplication', 'title_ru')
                    ->searchable()
                    ->required()
                    ->preload()
                    ->reactive(),

                    Forms\Components\Select::make('type_service_id')
                    ->label('Тип услуги')
                    ->options(function (callable $get) {
                        $typeApplicationId = $get('type_application_id');
                
                        if (!$typeApplicationId) {
                            return [];
                        }
                
                        return \App\Models\TypeService::where('type_application_id', $typeApplicationId)
                            ->pluck('title_ru', 'id');
                    })
                    ->searchable()
                    ->required()
                    ->preload()
                    ->disabled(fn (callable $get) => !$get('type_application_id')),

                Forms\Components\TextInput::make('count')
                    ->label('Количество')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('userRequestCount')
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
                Tables\Columns\TextColumn::make('count')
                ->label('Количество')
                ->searchable()
                ->sortable(),
             
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
