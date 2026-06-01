<?php

namespace App\Filament\Resources\StaticPages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class AppPageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('messages.static_page'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('title_ar')
                                    ->label(__('messages.title_ar'))
                                    ->weight('bold'),
                                TextEntry::make('title_en')
                                    ->label(__('messages.title_en'))
                                    ->weight('bold'),
                                TextEntry::make('status')
                                    ->label(__('messages.active_question'))
                                    ->badge()
                                    ->color(fn ($state) => $state === 'active' ? 'success' : 'danger')
                                    ->formatStateUsing(fn ($state) => $state === 'active' ? __('messages.active') : __('messages.inactive')),
                                Grid::make(1)->schema([
                                    TextEntry::make('content_ar')
                                        ->label(__('messages.content_ar'))
                                        ->html()
                                        ->columnSpanFull()
                                        ->extraAttributes(['class' => 'p-4 bg-gray-50 dark:bg-gray-800 rounded-lg']),
                                    TextEntry::make('content_en')
                                        ->label(__('messages.content_en'))
                                        ->html()
                                        ->columnSpanFull()
                                        ->extraAttributes(['class' => 'p-4 bg-gray-50 dark:bg-gray-800 rounded-lg']),
                                ])->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
