<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Filament\Resources\FaqResource\RelationManagers;
use App\Models\Faq;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Вопросы общие';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Блок подсказки
                Forms\Components\Section::make('Доступные переменные для использования в ответе')
                    ->description('Используйте эти переменные в поле "Ответ". Они будут автоматически заменены на актуальные значения.')
                    ->schema([
                        Forms\Components\ViewField::make('variables_hint')
                            ->view('filament.forms.components.faq-variables-hint')
                            ->viewData([
                                'variables' => [
                                    '{adress}' => 'Полный адрес фирмы',
                                    '{time}' => 'Режим работы',
                                    '{phone}' => 'Ссылка на кнопку показать номер телефона',
                                    '{funeral}' => 'Цена похороны',
                                    '{cremation}' => 'Цена кремации',
                                    '{cargo200}' => 'Цена груз 200',
                                    '{stonegrave}' => 'Цена памятника',
                                    '{fence}' => 'Цена оградка',
                                    '{dinner}' => 'Цена поминальный обед',
                                    '{halldinner}' => 'Цена поминальный обед в зале',
                                    '{wreath}' => 'Цена венок',
                                    '{coffin}' => 'Цена гроб',
                                    '{hearse}' => 'Цена аренды катафалка',
                                    '{tiles}' => 'Цена плитка на могилу',
                                    '{vase}' => 'Цена ваза на могилу',
                                    '{cross}' => 'Цена крест на могилу',
                                    '{photo}' => 'Цена фото на памятник',
                                    '{hall}' => 'Цена прощальный зал',
                                    '{urn}' => 'Цена урна для праха',
                                    '{euthanasia}' => 'Цена усыпления животных',
                                    '{animalcremation}' => 'Цена кремации животных',
                                    '{stonegraveanimal}' => 'Цена памятник для животных',
                                    '{urnanimal}' => 'Цена урна для животных',
                                ]
                            ])
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull(),

                // Основные поля
                Forms\Components\TextInput::make('title')
                    ->label('Вопрос')
                    ->required()
                    ->maxLength(255),

                RichEditor::make('content')
                    ->label('Ответ')
                    ->required()
                    ->helperText('Используйте переменные из подсказки выше. Например: "Наш адрес: {adress}, телефон: {phone}"'),

                Select::make('type_object')
                    ->label('Тип объекта')
                    ->options([
                        'usual' => 'Обычные',
                        'category_price_list' => 'Категории прайс листа',
                        'category_product' => 'Категории продуктов',
                        'cemeteries' => 'Кладбища',
                        'columbaria' => 'Колумбарии',
                        'crematoria' => 'Крематории',
                        'mortuaries' => 'Морги',
                        'organizations' => 'Организации',
                        'product_price_lists' => 'Продукты прайс листа',
                        'services' => 'Услуги',
                    ])
                    ->default('usual')
                    ->required()
                    ->searchable()
                    ->placeholder('Выберите тип объекта'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('title')
                    ->label('Вопрос')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                    
                Tables\Columns\TextColumn::make('type_object')
                    ->label('Тип объекта')
                    ->formatStateUsing(function ($state) {
                        $types = [
                            'usual' => 'Обычные',
                            'category_price_list' => 'Категории прайс листа',
                            'category_product' => 'Категории продуктов',
                            'cemeteries' => 'Кладбища',
                            'columbaria' => 'Колумбарии',
                            'crematoria' => 'Крематории',
                            'mortuaries' => 'Морги',
                            'organizations' => 'Организации',
                            'product_price_lists' => 'Продукты прайс листа',
                            'services' => 'Услуги',
                        ];
                        return $types[$state] ?? $state;
                    })
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('content')
                    ->label('Используемые переменные')
                    ->getStateUsing(function ($record) {
                        $content = $record->content ?? '';
                        $variables = [
                            '{adress}', '{time}', '{phone}', '{funeral}', '{cremation}', 
                            '{cargo200}', '{stonegrave}', '{fence}', '{dinner}', '{halldinner}',
                            '{wreath}', '{coffin}', '{hearse}', '{tiles}', '{vase}', '{cross}',
                            '{photo}', '{hall}', '{urn}', '{euthanasia}', '{animalcremation}',
                            '{stonegraveanimal}', '{urnanimal}'
                        ];
                        
                        $usedVariables = [];
                        foreach ($variables as $variable) {
                            if (str_contains($content, $variable)) {
                                $usedVariables[] = $variable;
                            }
                        }
                        
                        return !empty($usedVariables) ? implode(', ', $usedVariables) : 'Нет переменных';
                    })
                    ->searchable(false)
                    ->sortable(false)
                    ->limit(30)
                    ->color(fn ($state) => $state === 'Нет переменных' ? 'gray' : 'success')
                    ->tooltip(function ($state) {
                        return $state;
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type_object')
                    ->label('Тип объекта')
                    ->options([
                        'usual' => 'Обычные',
                        'category_price_list' => 'Категории прайс листа',
                        'category_product' => 'Категории продуктов',
                        'cemetery' => 'Кладбища',
                        'columbarium' => 'Колумбарии',
                        'crematorium' => 'Крематории',
                        'mortuary' => 'Морги',
                        'organization' => 'Организации',
                        'product_price_list' => 'Продукты прайс листа',
                        'service' => 'Услуги',
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
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public static function canViewAny(): bool
    {
        return static::shouldRegisterNavigation();
    }
}