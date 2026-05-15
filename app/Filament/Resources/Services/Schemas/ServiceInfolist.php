<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class ServiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(12)
                            ->schema([
                                ImageEntry::make('image')
                                    ->label('')
                                    ->disk('public')
                                    ->columnSpan(3)
                                    ->extraImgAttributes(['class' => 'rounded-xl shadow-md']),
                                
                                Group::make([
                                    TextEntry::make('service_ar')
                                        ->label('')
                                        ->weight('bold')
                                        ->size('lg')
                                        ->color('primary'),
                                    TextEntry::make('service_en')
                                        ->label('')
                                        ->size('md')
                                        ->color('gray'),
                                    
                                    Grid::make(2)->schema([
                                        TextEntry::make('provider.title_ar')
                                            ->label(__('messages.service_provider'))
                                            ->icon('heroicon-m-user-group'),
                                        TextEntry::make('subCategory.name_ar')
                                            ->label(__('messages.sub_category'))
                                            ->icon('heroicon-m-tag'),
                                    ]),

                                    TextEntry::make('price')
                                        ->label(__('messages.price'))
                                        ->weight('bold')
                                        ->color('success')
                                        ->money('SAR'),
                                ])->columnSpan(9),
                            ]),
                    ]),

                Section::make(__('messages.description_ar'))
                    ->schema([
                        TextEntry::make('description_ar')
                            ->label('')
                            ->markdown(),
                    ])
                    ->collapsible(),

                Section::make(__('messages.description_en'))
                    ->schema([
                        TextEntry::make('description_en')
                            ->label('')
                            ->markdown(),
                    ])
                    ->collapsible(),
            ]);
    }
}
