<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SearchBurialResource\Pages;
use App\Models\SearchBurial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class SearchBurialResource extends Resource
{
    protected static ?string $model = SearchBurial::class;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static ?string $navigationLabel = 'Поиск захоронений';
    protected static ?string $navigationGroup = 'Захоронения';

    protected static ?string $modelLabel = 'заявка на поиск захоронения';

    protected static ?string $pluralModelLabel = 'Заявки на поиск захоронений';
    
    
    public static function getNavigationBadge(): ?string
    {
        $count = SearchBurial::where('status', 0)->count();
        return $count > 0 ? (string) $count : null;
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger'; // Красный цвет для уведомлений
    }
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('surname')
                            ->label('Фамилия')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('name')
                            ->label('Имя')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('patronymic')
                            ->label('Отчество')
                            ->maxLength(255),
                            
                        Forms\Components\DatePicker::make('date_birth')
                            ->label('Дата рождения')
                            ->displayFormat('d.m.Y')
                            ->format('d.m.Y'),
                            
                        Forms\Components\DatePicker::make('date_death')
                            ->label('Дата смерти')
                            ->displayFormat('d.m.Y')
                            ->format('d.m.Y'),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Местоположение')
                    ->schema([
                        Forms\Components\Textarea::make('location')
                            ->label('Местоположение')
                            ->columnSpanFull(),
                            
                        Forms\Components\Textarea::make('landmark')
                            ->label('Ориентир')
                            ->columnSpanFull(),
                    ]),
                    
                Forms\Components\Section::make('Фотографии')
                    ->schema([
                        Forms\Components\FileUpload::make('image_attachments')
                            ->label('Фотографии захоронения')
                            ->multiple()
                            ->directory('burial_search')
                            ->image()
                            ->maxFiles(5)
                            ->maxSize(5120) // 5MB
                            ->preserveFilenames()
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->reorderable()
                            ->columnSpanFull(),
                            
                        Forms\Components\Placeholder::make('existing_photos')
                            ->label('Загруженные фотографии')
                            ->content(function ($record) {
                                if (!$record || empty($record->image_attachments)) {
                                    return 'Нет загруженных фотографий';
                                }
                                
                                $html = '<div class="grid grid-cols-4 gap-2">';
                                foreach ($record->image_attachments as $attachment) {
                                    if (isset($attachment['path'])) {
                                        $url = Storage::url($attachment['path']);
                                        $filename = $attachment['original_name'] ?? $attachment['filename'] ?? 'Фото';
                                        $html .= '
                                            <div class="relative">
                                                <a href="' . $url . '" target="_blank" class="block">
                                                    <img src="' . $url . '" alt="' . $filename . '" class="w-full h-24 object-cover rounded">
                                                </a>
                                                <div class="text-xs truncate mt-1">' . $filename . '</div>
                                            </div>
                                        ';
                                    }
                                }
                                $html .= '</div>';
                                
                                return $html;
                            })
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record && !empty($record->image_attachments)),
                    ]),
                    
                Forms\Components\Section::make('Пользователь и статус')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Пользователь')
                            ->relationship(
                                name: 'user',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => $query->whereNotNull('name')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name ?? 'Без имени')
                            ->required()
                            ->searchable()
                            ->preload(),
                            
                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options([
                                0 => 'Новая',
                                1 => 'В обработке',
                                2 => 'Выполнена',
                                3 => 'Отклонена',
                            ])
                            ->default(0)
                            ->required(),
                            
                        Forms\Components\Toggle::make('paid')
                            ->label('Оплачено')
                            ->default(false),
                            
                        Forms\Components\TextInput::make('price')
                            ->label('Цена')
                            ->numeric()
                            ->default(0)
                            ->prefix('₽'),
                            
                        Forms\Components\Textarea::make('reason_failure')
                            ->label('Причина отказа')
                            ->columnSpanFull(),
                            
                        Forms\Components\Textarea::make('additional_info')
                            ->label('Дополнительная информация')
                            ->columnSpanFull(),
                            
                        Forms\Components\DatePicker::make('completed_at')
                            ->label('Дата выполнения')
                            ->displayFormat('d.m.Y')
                            ->format('d.m.Y'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('surname')
                    ->label('Фамилия')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Имя')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('patronymic')
                    ->label('Отчество')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('date_birth')
                    ->label('Дата рождения')
                    ->date('d.m.Y')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('date_death')
                    ->label('Дата смерти')
                    ->date('d.m.Y')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('landmark')
                    ->label('Ориентир')
                    ->limit(30)
                    ->tooltip(fn ($record): string => $record->landmark ?? '')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('location')
                    ->label('Местоположение')
                    ->limit(30)
                    ->tooltip(fn ($record): string => $record->location ?? '')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\SelectColumn::make('status')
                    ->label('Статус')
                    ->options([
                        0 => 'Новая',
                        1 => 'В обработке',
                        2 => 'Выполнена',
                        3 => 'Отклонена',
                    ]),
                    
                Tables\Columns\IconColumn::make('paid')
                    ->label('Оплата')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('price')
                    ->label('Цена')
                    ->money('RUB')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('photos_count')
                    ->label('Фото')
                    ->getStateUsing(function ($record) {
                        if (empty($record->image_attachments)) {
                            return '0';
                        }
                        return count($record->image_attachments) . ' шт.';
                    })
                    ->badge()
                    ->color(fn ($state): string => str_contains($state, '0') ? 'gray' : 'success'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        0 => 'Новая',
                        1 => 'В обработке',
                        2 => 'Выполнена',
                        3 => 'Отклонена',
                    ]),
                    
                Tables\Filters\Filter::make('paid')
                    ->label('Оплачено')
                    ->query(fn ($query) => $query->where('paid', true)),
                    
                Tables\Filters\Filter::make('not_paid')
                    ->label('Не оплачено')
                    ->query(fn ($query) => $query->where('paid', false)),
                    
                Tables\Filters\Filter::make('has_photos')
                    ->label('Есть фото')
                    ->query(fn ($query) => $query->whereNotNull('image_attachments')),
                    
                Tables\Filters\Filter::make('no_photos')
                    ->label('Без фото')
                    ->query(fn ($query) => $query->whereNull('image_attachments')),
                
                Tables\Filters\Filter::make('location')
                    ->label('Поиск по адресу')
                    ->form([
                        Forms\Components\TextInput::make('location_search')
                            ->label('Адрес')
                            ->placeholder('Введите часть адреса...'),
                    ])
                    ->query(function ($query, array $data) {
                        if (!empty($data['location_search'])) {
                            $searchTerm = $data['location_search'];
                            return $query->where('location', 'like', "%{$searchTerm}%");
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view_photos')
                    ->label('Фото')
                    ->icon('heroicon-o-photo')
                    ->modalHeading('Фотографии захоронения')
                    ->modalContent(function ($record) {
                        if (empty($record->image_attachments)) {
                            return '<p class="p-4">Нет загруженных фотографий</p>';
                        }
                        
                        $html = '<div class="grid grid-cols-3 gap-4 p-4">';
                        foreach ($record->image_attachments as $attachment) {
                            if (isset($attachment['path'])) {
                                $url = Storage::url($attachment['path']);
                                $filename = $attachment['original_name'] ?? $attachment['filename'] ?? 'Фото';
                                $html .= '
                                    <div class="border rounded p-2">
                                        <a href="' . $url . '" target="_blank" class="block mb-2">
                                            <img src="' . $url . '" alt="' . $filename . '" class="w-full h-48 object-cover rounded">
                                        </a>
                                        <div class="text-sm text-gray-600 truncate">' . $filename . '</div>
                                    </div>
                                ';
                            }
                        }
                        $html .= '</div>';
                        
                        return $html;
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->hidden(fn ($record) => empty($record->image_attachments)),
                    
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
            // Добавьте отношения при необходимости
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSearchBurials::route('/'),
            'create' => Pages\CreateSearchBurial::route('/create'),
            'edit' => Pages\EditSearchBurial::route('/{record}/edit'),
        ];
    }
}