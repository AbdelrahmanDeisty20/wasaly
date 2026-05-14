<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make()
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(12)
                            ->schema([
                                ImageEntry::make('image')
                                    ->label('')
                                    ->disk('public')
                                    ->columnSpan(3)
                                    ->extraImgAttributes(['class' => 'rounded-xl shadow-md']),
                                
                                \Filament\Schemas\Components\Group::make([
                                    TextEntry::make('name_ar')
                                        ->label('')
                                        ->weight('bold')
                                        ->size('lg')
                                        ->color('primary'),
                                    TextEntry::make('name_en')
                                        ->label('')
                                        ->size('md')
                                        ->color('gray'),
                                    
                                    TextEntry::make('status')
                                        ->label(__('messages.status'))
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            'active' => 'success',
                                            'inactive' => 'danger',
                                            default => 'gray',
                                        }),
                                ])->columnSpan(9),
                            ]),
                    ]),

                \Filament\Schemas\Components\Section::make(__('messages.sub_categories'))
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('subCategories')
                            ->label('')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(4)
                                    ->schema([
                                        ImageEntry::make('image')
                                            ->label('')
                                            ->disk('public')
                                            ->circular(),
                                        TextEntry::make('name_ar')
                                            ->label(__('messages.service_ar'))
                                            ->weight('bold'),
                                        TextEntry::make('name_en')
                                            ->label(__('messages.service_en')),
                                        TextEntry::make('status')
                                            ->label(__('messages.status'))
                                            ->badge(),
                                    ]),
                            ])
                            ->grid(2)
                            ->placeholder('لا توجد أقسام فرعية حالياً'),
                    ])
                    ->collapsible(),
            ]);
    }
}
