<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class ReviewInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('messages.review_details'))
                    ->schema([
                        Grid::make(12)
                            ->schema([
                                Group::make([
                                    TextEntry::make('user.full_name')
                                        ->label(__('messages.user'))
                                        ->weight('bold')
                                        ->size('lg')
                                        ->color('primary')
                                        ->icon('heroicon-m-user'),

                                    TextEntry::make('rating')
                                        ->label(__('messages.rating'))
                                        ->badge()
                                        ->color(fn ($state) => $state >= 4 ? 'success' : ($state >= 3 ? 'warning' : 'danger'))
                                        ->formatStateUsing(fn ($state) => $state . ' ⭐')
                                        ->weight('bold'),

                                    Grid::make(3)->schema([
                                        TextEntry::make('product_id')
                                            ->label(__('messages.product'))
                                            ->state(fn ($record) => $record->product?->name_ar ?? $record->product?->name_en)
                                            ->icon('heroicon-m-shopping-bag')
                                            ->placeholder('-'),

                                        TextEntry::make('service_id')
                                            ->label(__('messages.services'))
                                            ->state(fn ($record) => $record->service?->service_ar ?? $record->service?->service_en)
                                            ->icon('heroicon-m-wrench')
                                            ->placeholder('-'),

                                        TextEntry::make('provider_id')
                                            ->label(__('messages.product_owner'))
                                            ->state(fn ($record) => $record->provider?->title_ar ?? $record->provider?->title_en)
                                            ->icon('heroicon-m-building-storefront')
                                            ->placeholder('-'),
                                    ])->extraAttributes(['class' => 'mt-4']),

                                    TextEntry::make('comment')
                                        ->label(__('messages.comment'))
                                        ->icon('heroicon-m-chat-bubble-bottom-center-text')
                                        ->columnSpanFull()
                                        ->extraAttributes(['class' => 'p-4 bg-gray-50 dark:bg-gray-800 rounded-lg italic']),

                                    Grid::make(2)->schema([
                                        TextEntry::make('created_at')
                                            ->label(__('messages.created_at'))
                                            ->dateTime()
                                            ->icon('heroicon-m-calendar'),

                                        TextEntry::make('updated_at')
                                            ->label(__('messages.updated_at'))
                                            ->dateTime()
                                            ->icon('heroicon-m-pencil-square'),
                                    ])->extraAttributes(['class' => 'mt-4']),

                                ])->columnSpan(12),
                            ]),
                    ]),
            ]);
    }
}
