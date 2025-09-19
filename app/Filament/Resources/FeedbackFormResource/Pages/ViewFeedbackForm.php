<?php

namespace App\Filament\Resources\FeedbackFormResource\Pages;

use App\Filament\Resources\FeedbackFormResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;

class ViewFeedbackForm extends ViewRecord
{
    protected static string $resource = FeedbackFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Удалить')
                ->icon('heroicon-o-trash'),
                
            Actions\Action::make('back')
                ->label('Назад к списку')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Детальная информация о заявке')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('topic')
                                    ->label('Тема вопроса')
                                    ->badge()
                                    ->color('primary'),
                                    
                                Components\TextEntry::make('created_at')
                                    ->label('Дата создания')
                                    ->dateTime('d.m.Y H:i')
                                    ->color('gray'),
                            ]),
                            
                        Components\TextEntry::make('name')
                            ->label('Имя клиента')
                            ->icon('heroicon-o-user')
                            ->extraAttributes(['class' => 'font-medium']),
                            
                        Components\TextEntry::make('phone')
                            ->label('Номер телефона')
                            ->icon('heroicon-o-phone')
                            ->url(fn ($record) => "tel:{$record->phone}")
                            ->color('primary'),
                            
                        Components\TextEntry::make('question')
                            ->label('Вопрос клиента')
                            ->markdown()
                            ->prose()
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'bg-gray-50 p-4 rounded-lg']),
                            
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('created_at')
                                    ->label('Создано')
                                    ->dateTime('d.m.Y H:i:s')
                                    ->icon('heroicon-o-calendar')
                                    ->color('gray'),
                                    
                                Components\TextEntry::make('updated_at')
                                    ->label('Обновлено')
                                    ->dateTime('d.m.Y H:i:s')
                                    ->icon('heroicon-o-clock')
                                    ->color('gray'),
                            ]),
                    ])
                    ->columns(1),
                    
                Components\Section::make('Дополнительная информация')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Components\TextEntry::make('id')
                            ->label('ID заявки')
                            ->badge()
                            ->color('gray'),
                            
                        Components\TextEntry::make('question')
                            ->label('Количество символов в вопросе')
                            ->state(fn ($record) => strlen($record->question))
                            ->suffix(' символов')
                            ->icon('heroicon-o-document-text'),
                            
                        Components\TextEntry::make('created_at')
                            ->label('Прошло времени с момента создания')
                            ->state(fn ($record) => $record->created_at->diffForHumans())
                            ->icon('heroicon-o-clock'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(true),
            ]);
    }

   
}