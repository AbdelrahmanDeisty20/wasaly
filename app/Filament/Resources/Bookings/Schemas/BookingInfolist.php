<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class BookingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('messages.order_info'))
                    ->schema([
                        Grid::make(12)
                            ->schema([
                                Group::make([
                                    TextEntry::make('id')
                                        ->label(__('messages.order_number'))
                                        ->weight('bold')
                                        ->color('primary')
                                        ->prefix('#'),
                                    
                                    Grid::make(2)->schema([
                                        TextEntry::make('user.name')
                                            ->label(__('messages.user'))
                                            ->icon('heroicon-m-user'),
                                        TextEntry::make('status')
                                            ->label(__('messages.status'))
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'pending' => 'gray',
                                                'accepted' => 'info',
                                                'processing' => 'warning',
                                                'shipped' => 'primary',
                                                'delivered' => 'success',
                                                'cancelled' => 'danger',
                                                default => 'gray',
                                            })
                                            ->formatStateUsing(fn (string $state): string => __("messages.{$state}")),
                                    ]),
                                ])->columnSpan(12),
                            ]),
                    ]),

                Section::make(__('messages.service_ar'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('service.service_ar')
                                    ->label(__('messages.service_ar'))
                                    ->weight('bold'),
                                TextEntry::make('provider.title_ar')
                                    ->label(__('messages.service_provider')),
                                TextEntry::make('date')
                                    ->label(__('messages.date_required'))
                                    ->date('d M Y')
                                    ->icon('heroicon-m-calendar'),
                                TextEntry::make('time')
                                    ->label(__('messages.time_required'))
                                    ->icon('heroicon-m-clock'),
                            ]),
                    ]),

                Section::make(__('messages.problem_description'))
                    ->schema([
                        TextEntry::make('problem_description')
                            ->label('')
                            ->placeholder('لا يوجد وصف للمشكلة'),
                    ])
                    ->collapsible(),
            ]);
    }
}
