<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class TagsRelationManager extends RelationManager
{
    protected static string $relationship = 'tags';

    protected static ?string $title = 'Хэштеги категории';

    protected static ?string $modelLabel = 'хэштег';

    protected static ?string $pluralModelLabel = 'хэштеги';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Тег/Хэштег')
                    ->required()
                    ->maxLength(191)
                    ->placeholder('Например: Кресты на могилу из гранита')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (!empty($state)) {
                            $set('slug', Str::slug($state));
                        }
                    })
                    ->helperText('Популярный поисковый запрос пользователей'),
                
                Forms\Components\TextInput::make('slug')
                    ->label('Slug (URL)')
                    ->required()
                    ->maxLength(191)
                    ->unique(ignoreRecord: true)
                    ->helperText('Автоматически генерируется из названия'),
                
                Forms\Components\Select::make('tag_type')
                    ->label('Тип тега')
                    ->required()
                    ->options([
                        Tag::TYPE_POPULAR => 'Популярные запросы',
                        Tag::TYPE_RELATED => 'С этим также ищут',
                        Tag::TYPE_MATERIAL => 'Материал',
                        Tag::TYPE_STYLE => 'Стиль',
                        Tag::TYPE_FILTER => 'Фильтр',
                        Tag::TYPE_SEO => 'SEO тег',
                        Tag::TYPE_BRAND => 'Бренд',
                    ])
                    ->default(Tag::TYPE_POPULAR)
                    ->helperText('Группировка тегов для разных блоков'),
                
                Forms\Components\TextInput::make('priority')
                    ->label('Приоритет')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(100)
                    ->helperText('Чем выше число, тем выше тег в списке (0-100)'),
                
                Forms\Components\Section::make('SEO оптимизация')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(255)
                            ->helperText('Заголовок страницы для поисковых систем'),
                        
                        Forms\Components\Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Описание страницы для поисковых систем'),
                    ])
                    ->collapsible(),
                
                Forms\Components\Section::make('Статистика')
                    ->schema([
                        Forms\Components\TextInput::make('search_count')
                            ->label('Счетчик поиска')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->helperText('Сколько раз искали по этому тегу'),
                        
                        Forms\Components\TextInput::make('click_count')
                            ->label('Счетчик кликов')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->helperText('Сколько раз кликали на этот тег'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                
                Forms\Components\Toggle::make('is_active')
                    ->label('Активен')
                    ->default(true)
                    ->inline(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Тег')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Tag $record): string {
                        return $record->name;
                    }),
                
                Tables\Columns\BadgeColumn::make('tag_type')
                    ->label('Тип')
                    ->formatStateUsing(function ($state) {
                        return match($state) {
                            Tag::TYPE_POPULAR => 'Популярные',
                            Tag::TYPE_RELATED => 'С этим ищут',
                            Tag::TYPE_MATERIAL => 'Материал',
                            Tag::TYPE_STYLE => 'Стиль',
                            Tag::TYPE_FILTER => 'Фильтр',
                            Tag::TYPE_SEO => 'SEO',
                            Tag::TYPE_BRAND => 'Бренд',
                            default => $state,
                        };
                    })
                    ->colors([
                        'success' => fn ($state) => in_array($state, [Tag::TYPE_POPULAR, Tag::TYPE_FILTER]),
                        'warning' => fn ($state) => in_array($state, [Tag::TYPE_RELATED]),
                        'info' => fn ($state) => in_array($state, [Tag::TYPE_MATERIAL, Tag::TYPE_STYLE]),
                        'gray' => fn ($state) => in_array($state, [Tag::TYPE_SEO, Tag::TYPE_BRAND]),
                    ])
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('priority')
                    ->label('Приоритет')
                    ->sortable()
                    ->alignCenter()
                    ->color(fn ($state) => $state > 50 ? 'success' : ($state > 20 ? 'warning' : 'gray')),
                
                Tables\Columns\TextColumn::make('search_count')
                    ->label('Поиски')
                    ->sortable()
                    ->alignCenter()
                    ->color('primary')
                    ->icon('heroicon-o-magnifying-glass'),
                
                Tables\Columns\TextColumn::make('click_count')
                    ->label('Клики')
                    ->sortable()
                    ->alignCenter()
                    ->color('success')
                    ->icon('heroicon-o-cursor-arrow-rays'),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('priority', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('tag_type')
                    ->label('Тип тега')
                    ->options([
                        Tag::TYPE_POPULAR => 'Популярные запросы',
                        Tag::TYPE_RELATED => 'С этим также ищут',
                        Tag::TYPE_MATERIAL => 'Материал',
                        Tag::TYPE_STYLE => 'Стиль',
                        Tag::TYPE_FILTER => 'Фильтр',
                        Tag::TYPE_SEO => 'SEO тег',
                        Tag::TYPE_BRAND => 'Бренд',
                    ])
                    ->multiple(),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активность')
                    ->boolean()
                    ->trueLabel('Только активные')
                    ->falseLabel('Только неактивные')
                    ->native(false),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить хэштег')
                    ->modalHeading('Новый хэштег для категории')
                    ->successNotificationTitle('Хэштег добавлен'),
                
                // Массовое добавление тегов из текста
                Tables\Actions\Action::make('bulk_import')
                    ->label('Массовый импорт')
                    ->icon('heroicon-o-document-plus')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('tag_type')
                            ->label('Тип тегов')
                            ->required()
                            ->options([
                                Tag::TYPE_POPULAR => 'Популярные запросы',
                                Tag::TYPE_RELATED => 'С этим также ищут',
                                Tag::TYPE_MATERIAL => 'Материал',
                                Tag::TYPE_STYLE => 'Стиль',
                                Tag::TYPE_FILTER => 'Фильтр',
                            ])
                            ->default(Tag::TYPE_POPULAR),
                        
                        Forms\Components\Textarea::make('tags')
                            ->label('Теги (через запятую)')
                            ->required()
                            ->rows(10)
                            ->placeholder("Кресты на могилу из гранита\nКресты на могилу из металла\nКресты на могилу из дерева")
                            ->helperText('Каждый тег с новой строки или через запятую'),
                        
                        Forms\Components\TextInput::make('priority')
                            ->label('Приоритет')
                            ->numeric()
                            ->default(10)
                            ->minValue(0)
                            ->maxValue(100),
                    ])
                    ->action(function (array $data, $livewire) {
                        $tags = preg_split('/[\n,]+/', $data['tags'], -1, PREG_SPLIT_NO_EMPTY);
                        $tags = array_map('trim', $tags);
                        
                        $created = 0;
                        $skipped = 0;
                        $now = now();
                        
                        foreach ($tags as $tagName) {
                            if (empty($tagName)) continue;
                            
                            // Проверяем, существует ли уже такой тег
                            $exists = Tag::where('entity_type', Tag::ENTITY_CATEGORY_PRODUCT)
                                ->where('entity_id', $livewire->ownerRecord->id)
                                ->where('name', $tagName)
                                ->exists();
                            
                            if (!$exists) {
                                Tag::create([
                                    'entity_type' => Tag::ENTITY_CATEGORY_PRODUCT,
                                    'entity_id' => $livewire->ownerRecord->id,
                                    'name' => $tagName,
                                    'slug' => Str::slug($tagName),
                                    'tag_type' => $data['tag_type'],
                                    'priority' => $data['priority'],
                                    'is_active' => true,
                                    'meta_title' => "Купить {$tagName} недорого в Москве | Каталог 2024",
                                    'meta_description' => "Большой выбор {$tagName}. Цены от производителей. Фото, отзывы. Доставка по России.",
                                ]);
                                $created++;
                            } else {
                                $skipped++;
                            }
                        }
                        
                        $this->sendSuccessNotification(
                            "Импорт завершен! Добавлено: {$created}, Пропущено (дубликаты): {$skipped}"
                        );
                    })
                    ->modalWidth('4xl')
                    ->modalHeading('Массовый импорт тегов'),
            ])
            ->actions([
                Tables\Actions\Action::make('increment_search')
                    ->label('+ Поиск')
                    ->icon('heroicon-o-magnifying-glass')
                    ->color('gray')
                    ->action(function (Tag $record) {
                        $record->increment('search_count');
                        $this->sendSuccessNotification('Счетчик поиска увеличен');
                    })
                    ->visible(fn (Tag $record) => auth()->user()->can('update', $record)),
                
                Tables\Actions\Action::make('increment_click')
                    ->label('+ Клик')
                    ->icon('heroicon-o-cursor-arrow-rays')
                    ->color('gray')
                    ->action(function (Tag $record) {
                        $record->increment('click_count');
                        $this->sendSuccessNotification('Счетчик кликов увеличен');
                    })
                    ->visible(fn (Tag $record) => auth()->user()->can('update', $record)),
                
                Tables\Actions\EditAction::make()
                    ->modalHeading('Редактировать хэштег'),
                
                Tables\Actions\DeleteAction::make()
                    ->modalHeading('Удалить хэштег'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Удалить выбранные')
                        ->modalHeading('Удалить выбранные хэштеги'),
                    
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Активировать')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_active' => true]);
                            }
                            $this->sendSuccessNotification('Хэштеги активированы');
                        })
                        ->deselectRecordsAfterCompletion(),
                    
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Деактивировать')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_active' => false]);
                            }
                            $this->sendSuccessNotification('Хэштеги деактивированы');
                        })
                        ->deselectRecordsAfterCompletion(),
                    
                    Tables\Actions\BulkAction::make('update_priority')
                        ->label('Изменить приоритет')
                        ->icon('heroicon-o-arrow-up-circle')
                        ->color('warning')
                        ->form([
                            Forms\Components\TextInput::make('priority')
                                ->label('Новый приоритет')
                                ->numeric()
                                ->required()
                                ->minValue(0)
                                ->maxValue(100),
                        ])
                        ->action(function ($records, array $data) {
                            foreach ($records as $record) {
                                $record->update(['priority' => $data['priority']]);
                            }
                            $this->sendSuccessNotification('Приоритет обновлен');
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateHeading('Нет хэштегов')
            ->emptyStateDescription('Добавьте хэштеги для этой категории')
            ->emptyStateIcon('heroicon-o-tag')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить хэштег')
                    ->modalHeading('Новый хэштег для категории'),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}