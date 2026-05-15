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
                Section::make()
                    ->schema([
                        Grid::make(12)
                            ->schema([
                                ImageEntry::make('cover')
                                    ->label('')
                                    ->disk('public')
                                    ->columnSpan(3)
                                    ->extraImgAttributes(['class' => 'rounded-xl shadow-md']),
                                
                                Group::make([
                                    TextEntry::make('title_ar')
                                        ->label('')
                                        ->weight('bold')
                                        ->size('lg')
                                        ->color('primary'),
                                    TextEntry::make('title_en')
                                        ->label('')
                                        ->size('md')
                                        ->color('gray'),
                                    
                                    Grid::make(2)->schema([
                                        TextEntry::make('user.name')
                                            ->label(__('messages.user'))
                                            ->icon('heroicon-m-user'),
                                        TextEntry::make('subCategory.name_ar')
                                            ->label(__('messages.sub_category'))
                                            ->icon('heroicon-m-tag'),
                                    ]),

                                    TextEntry::make('status')
                                        ->label(__('messages.status'))
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            'active' => 'success',
                                            'inactive' => 'danger',
                                            default => 'gray',
                                        })
                                        ->formatStateUsing(fn (string $state): string => __("messages.{$state}")),
                                ])->columnSpan(9),
                            ]),
                    ]),

                Section::make(__('messages.description_ar'))
                    ->schema([
                        TextEntry::make('service_description_ar')
                            ->label('')
                            ->markdown(),
                    ])
                    ->collapsible(),

                Section::make(__('messages.description_en'))
                    ->schema([
                        TextEntry::make('service_description_en')
                            ->label('')
                            ->markdown(),
                    ])
                    ->collapsible(),
            ]);
    }
}
