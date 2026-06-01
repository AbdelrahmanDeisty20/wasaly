<?php

namespace App\Filament\Resources\Offers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class OfferInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('messages.offer_details'))
                    ->schema([
                        Grid::make(12)
                            ->schema([
                                Group::make([
                                    TextEntry::make('product.name_ar')
                                        ->label(__('messages.product'))
                                        ->weight('bold')
                                        ->size('lg')
                                        ->color('primary')
                                        ->icon('heroicon-m-shopping-bag'),

                                    Grid::make(2)->schema([
                                        TextEntry::make('discount_percentage')
                                            ->label(__('messages.discount_percentage'))
                                            ->suffix('%')
                                            ->weight('bold')
                                            ->color('danger')
                                            ->icon('heroicon-m-tag'),

                                        IconEntry::make('is_active')
                                            ->label(__('messages.active_question'))
                                            ->boolean(),

                                        TextEntry::make('start_date')
                                            ->label(__('messages.start_date'))
                                            ->dateTime()
                                            ->icon('heroicon-m-calendar'),

                                        TextEntry::make('end_date')
                                            ->label(__('messages.end_date'))
                                            ->dateTime()
                                            ->icon('heroicon-m-calendar'),
                                    ]),

                                    TextEntry::make('created_at')
                                        ->label(__('messages.created_at'))
                                        ->dateTime()
                                        ->color('gray'),

                                ])->columnSpan(12),
                            ]),
                    ]),
            ]);
    }
}
