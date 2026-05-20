<?php

namespace App\Filament\Resources\Providers\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class ProviderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Tabs::make('Provider View')
                    ->tabs([
                        \Filament\Schemas\Components\Tabs\Tab::make(__('messages.provider_details') ?? 'Provider Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(12)
                                    ->schema([
                                        \Filament\Schemas\Components\Group::make([
                                            \Filament\Schemas\Components\Section::make()
                                                ->schema([
                                                    \Filament\Schemas\Components\Grid::make(12)
                                                        ->schema([
                                                            \Filament\Infolists\Components\ImageEntry::make('cover')
                                                                ->label('')
                                                                ->disk('public')
                                                                ->state(function ($record) {
                                                                    if (!$record->cover) return null;
                                                                    return str_starts_with($record->cover, 'providers/') ? $record->cover : 'providers/' . $record->cover;
                                                                })
                                                                ->columnSpan(3)
                                                                ->circular()
                                                                ->size(100)
                                                                ->extraImgAttributes(['class' => 'shadow-md']),
                                                            
                                                            \Filament\Schemas\Components\Group::make([
                                                                TextEntry::make('title_ar')
                                                                    ->label('')
                                                                    ->weight('bold')
                                                                    ->size('lg')
                                                                    ->color('primary'),
                                                                TextEntry::make('title_en')
                                                                    ->label('')
                                                                    ->size('md')
                                                                    ->color('gray'),
                                                                
                                                                \Filament\Schemas\Components\Grid::make(2)
                                                                    ->schema([
                                                                        TextEntry::make('user.name')
                                                                            ->label(__('messages.user'))
                                                                            ->icon('heroicon-m-user'),
                                                                        TextEntry::make('subCategory.name_ar')
                                                                            ->label(__('messages.sub_category'))
                                                                            ->icon('heroicon-m-tag'),
                                                                    ])->extraAttributes(['class' => 'mt-4']),
                                                            ])->columnSpan(9),
                                                        ]),

                                                    \Filament\Schemas\Components\Section::make(__('messages.description_ar'))
                                                        ->schema([
                                                            TextEntry::make('service_description_ar')
                                                                ->label('')
                                                                ->markdown()
                                                                ->prose(),
                                                        ])
                                                        ->compact()
                                                        ->collapsible()
                                                        ->extraAttributes(['class' => 'mt-6']),

                                                    \Filament\Schemas\Components\Section::make(__('messages.description_en'))
                                                        ->schema([
                                                            TextEntry::make('service_description_en')
                                                                ->label('')
                                                                ->markdown()
                                                                ->prose(),
                                                        ])
                                                        ->compact()
                                                        ->collapsible(),
                                                ]),
                                        ])->columnSpan(9),

                                        \Filament\Schemas\Components\Group::make([
                                            \Filament\Schemas\Components\Section::make(__('messages.status') ?? 'Status')
                                                ->schema([
                                                    TextEntry::make('status')
                                                        ->label(__('messages.status'))
                                                        ->badge()
                                                        ->color(fn (string $state): string => match ($state) {
                                                            'active' => 'success',
                                                            'inactive' => 'danger',
                                                            default => 'gray',
                                                        })
                                                        ->formatStateUsing(fn (string $state): string => __("messages.{$state}")),
                                                ]),

                                            \Filament\Schemas\Components\Section::make(app()->getLocale() == 'ar' ? 'أوقات العمل' : 'Working Hours')
                                                ->schema([
                                                    \Filament\Schemas\Components\Grid::make(2)
                                                        ->schema([
                                                            TextEntry::make('from_day')
                                                                ->label(app()->getLocale() == 'ar' ? 'من يوم' : 'From Day')
                                                                ->formatStateUsing(fn (string $state): string => __("messages.{$state}")),
                                                            TextEntry::make('to_day')
                                                                ->label(app()->getLocale() == 'ar' ? 'إلى يوم' : 'To Day')
                                                                ->formatStateUsing(fn (string $state): string => __("messages.{$state}")),
                                                            TextEntry::make('start_time')
                                                                ->label(app()->getLocale() == 'ar' ? 'وقت البدء' : 'Start Time')
                                                                ->time('h:i A'),
                                                            TextEntry::make('end_time')
                                                                ->label(app()->getLocale() == 'ar' ? 'وقت الانتهاء' : 'End Time')
                                                                ->time('h:i A'),
                                                        ])
                                                ]),
                                        ])->columnSpan(3),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
