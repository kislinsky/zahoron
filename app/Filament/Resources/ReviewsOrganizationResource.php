<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\ReviewsOrganization;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ReviewsOrganizationResource\Pages;
use App\Filament\Resources\ReviewsOrganizationResource\RelationManagers;
use App\Models\City;

class ReviewsOrganizationResource extends Resource
{
    protected static ?string $model = ReviewsOrganization::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Отзывы об организациях';
    protected static ?string $navigationGroup = 'Организации';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        if (static::isRestrictedUser()) {
            $cityIds = static::getUserCityIds();
            
            if (!empty($cityIds)) {
                $query->whereIn('city_id', $cityIds);
            } else {
                $query->whereNull('city_id');
            }
        }
        
        return $query;
    }

    protected static function isRestrictedUser(): bool
    {
        return in_array(auth()->user()->role, ['deputy-admin', 'manager']);
    }

    protected static function getUserCityIds(): array
    {
        $user = auth()->user();
        $cityIds = [];
        
        if (!empty($user->city_ids)) {
            $decoded = json_decode($user->city_ids, true);
            
            if (is_array($decoded)) {
                $cityIds = $decoded;
            } else {
                $cityIds = array_filter(explode(',', trim($user->city_ids, '[],"')));
            }
            
            $cityIds = array_map('intval', array_filter($cityIds));
        }
        
        return $cityIds;
    }

    public static function form(Form $form): Form
    {
        $isRestrictedUser = static::isRestrictedUser();
        $userCityIds = $isRestrictedUser ? static::getUserCityIds() : [];
        
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Имя')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('rating')
                    ->label('Рейтинг')
                    ->numeric()
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('organization_id')
                    ->label('Организация')
                    ->relationship('organization', 'title')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('city_id')
                    ->label('Город')
                    ->options(function () use ($userCityIds, $isRestrictedUser) {
                        if ($isRestrictedUser) {
                            return City::whereIn('id', $userCityIds)->pluck('title', 'id');
                        }
                        return City::pluck('title', 'id');
                    })
                    ->required()
                    ->searchable()
                    ->disabled($isRestrictedUser),

                Forms\Components\Textarea::make('content')
                    ->label('Контент')
                    ->required()
                    ->maxLength(1000),

                Forms\Components\Textarea::make('organization_response')
                    ->label('Ответ организации')
                    ->maxLength(1000),

                Select::make('status')
                    ->label('Статус')
                    ->options([
                        0 => 'В обработке',
                        1 => 'Принят',
                    ])
                    ->required()
                    ->default(1),

                Placeholder::make('created_at')
                    ->label('Дата создания')
                    ->content(fn (?Model $record): string => $record?->created_at?->format('d.m.Y H:i:s') ?? ''),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('name')
                    ->label('Имя')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('organization.title')
                    ->label('Организация')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('city.title')
                    ->label('Город')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'В обработке',
                        1 => 'Принят',
                        default => 'Неизвестно',
                    })
                    ->sortable(),
                
                TextColumn::make('rating')
                    ->label('Рейтинг')
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('city_id')
                    ->label('Город')
                    ->relationship('city', 'title')
                    ->hidden(static::isRestrictedUser()),
                
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        0 => 'В обработке',
                        1 => 'Принят',
                    ]),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviewsOrganizations::route('/'),
            'create' => Pages\CreateReviewsOrganization::route('/create'),
            'edit' => Pages\EditReviewsOrganization::route('/{record}/edit'),
        ];
    }
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'admin' || 
               in_array(auth()->user()->role, ['deputy-admin', 'manager']);
    }

    public static function canViewAny(): bool
    {
        return static::shouldRegisterNavigation();
    }

    public static function canEdit(Model $record): bool
    {
        if (auth()->user()->role === 'admin') {
            return true;
        }
        
        if (static::isRestrictedUser()) {
            $userCityIds = static::getUserCityIds();
            return in_array($record->city_id, $userCityIds);
        }
        
        return false;
    }

    public static function canCreate(): bool
    {
        if (auth()->user()->role === 'admin') {
            return true;
        }
        
        if (static::isRestrictedUser()) {
            return !empty(static::getUserCityIds());
        }
        
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return static::canEdit($record);
    }
}