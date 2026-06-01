<?php

namespace App\Filament\Resources\Specifications\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class SpecificationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('messages.specification_details'))
                    ->schema([
                        Grid::make(12)
                            ->schema([
                                ImageEntry::make('icon')
                                    ->label('')
                                    ->disk('public')
                                    ->state(function ($record) {
                                        if (!$record->icon) return null;
                                        return str_starts_with($record->icon, 'specifications/') ? $record->icon : 'specifications/' . $record->icon;
                                    })
                                    ->columnSpan(3)
                                    ->circular()
                                    ->size(100)
                                    ->extraImgAttributes(['class' => 'shadow-md']),

                                Group::make([
                                    TextEntry::make('product.name_ar')
                                        ->label(__('messages.product'))
                                        ->weight('bold')
                                        ->size('lg')
                                        ->color('primary')
                                        ->icon('heroicon-m-shopping-bag'),

                                    Grid::make(2)->schema([
                                        TextEntry::make('key_ar')
                                            ->label(__('messages.key_ar'))
                                            ->icon('heroicon-m-list-bullet'),

                                        TextEntry::make('key_en')
                                            ->label(__('messages.key_en'))
                                            ->icon('heroicon-m-list-bullet'),

                                        TextEntry::make('value_ar')
                                            ->label(__('messages.value_ar'))
                                            ->icon('heroicon-m-information-circle'),

                                        TextEntry::make('value_en')
                                            ->label(__('messages.value_en'))
                                            ->icon('heroicon-m-information-circle'),
                                    ]),

                                    TextEntry::make('created_at')
                                        ->label(__('messages.created_at'))
                                        ->dateTime()
                                        ->color('gray'),

                                ])->columnSpan(9),
                            ]),
                    ]),
            ]);
    }
}
