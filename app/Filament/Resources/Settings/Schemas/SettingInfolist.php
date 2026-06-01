<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class SettingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('messages.setting'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('key_ar')
                                    ->label(__('messages.key') . ' (العربية)')
                                    ->weight('bold'),
                                TextEntry::make('key_en')
                                    ->label(__('messages.key') . ' (English)')
                                    ->weight('bold'),
                                TextEntry::make('value_ar')
                                    ->label(__('messages.value') . ' (العربية)')
                                    ->columnSpanFull()
                                    ->extraAttributes(['class' => 'p-4 bg-gray-50 dark:bg-gray-800 rounded-lg']),
                                TextEntry::make('value_en')
                                    ->label(__('messages.value') . ' (English)')
                                    ->columnSpanFull()
                                    ->extraAttributes(['class' => 'p-4 bg-gray-50 dark:bg-gray-800 rounded-lg']),
                            ]),
                    ]),
            ]);
    }
}
