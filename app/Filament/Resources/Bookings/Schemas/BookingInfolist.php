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
                Section::make(__('messages.booking_info'))
                    ->schema([
                        Grid::make(12)
                            ->schema([
                                Group::make([
                                    TextEntry::make('id')
                                        ->label(__('messages.booking_number'))
                                        ->weight('bold')
                                        ->color('primary')
                                        ->prefix('#'),
                                    
                                    Grid::make(2)->schema([
                                        TextEntry::make('user.full_name')
                                            ->label(__('messages.user'))
                                            ->icon('heroicon-m-user'),
                                        TextEntry::make('status')
                                            ->label(__('messages.status'))
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'pending' => 'gray',
                                                'accepted' => 'info',
                                                'confirmed' => 'info',
                                                'processing' => 'warning',
                                                'shipped' => 'primary',
                                                'delivered' => 'success',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                                default => 'gray',
                                            })
                                            ->formatStateUsing(fn (string $state): string => __("messages.{$state}")),
                                    ]),
                                ])->columnSpan(12),
                            ]),
                    ]),

                Section::make(__('messages.service_details'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('service.service_ar')
                                    ->label(__('messages.service_ar'))
                                    ->weight('bold'),
                                TextEntry::make('provider.title_ar')
                                    ->label(__('messages.service_provider')),
                                TextEntry::make('day')
                                    ->label(__('messages.day'))
                                    ->state(fn ($record) => 
                                        app()->getLocale() === 'ar' 
                                            ? ($record->availableDay?->name_ar ?? '-')
                                            : ($record->availableDay?->name_en ?? '-')
                                    )
                                    ->icon('heroicon-m-calendar'),
                                TextEntry::make('time')
                                    ->label(__('messages.time'))
                                    ->state(fn ($record) => 
                                        $record->custom_time 
                                        ?? $record->availableTime?->time 
                                        ?? '-'
                                    )
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
