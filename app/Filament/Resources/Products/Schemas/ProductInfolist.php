<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Tabs::make('Product View')
                    ->tabs([
                        \Filament\Schemas\Components\Tabs\Tab::make(__('messages.product_details'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(12)
                                    ->schema([
                                        \Filament\Schemas\Components\Group::make([
                                            \Filament\Schemas\Components\Section::make()
                                                ->schema([
                                                    \Filament\Schemas\Components\Grid::make(12)
                                                        ->schema([
                                                            \Filament\Infolists\Components\ImageEntry::make('image')
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
                                                                
                                                                \Filament\Schemas\Components\Grid::make(2)
                                                                    ->schema([
                                                                        TextEntry::make('subCategory.name_ar')
                                                                            ->label(__('messages.sub_category'))
                                                                            ->icon('heroicon-m-tag'),
                                                                        TextEntry::make('brand.name_ar')
                                                                            ->label(__('messages.brand'))
                                                                            ->icon('heroicon-m-bookmark')
                                                                            ->placeholder('-'),
                                                                    ])->extraAttributes(['class' => 'mt-4']),
                                                            ])->columnSpan(9),
                                                        ]),

                                                    \Filament\Schemas\Components\Section::make(__('messages.description_ar'))
                                                        ->schema([
                                                            TextEntry::make('description_ar')
                                                                ->label('')
                                                                ->markdown()
                                                                ->prose(),
                                                        ])
                                                        ->compact()
                                                        ->collapsible()
                                                        ->extraAttributes(['class' => 'mt-6']),

                                                    \Filament\Schemas\Components\Section::make(__('messages.description_en'))
                                                        ->schema([
                                                            TextEntry::make('description_en')
                                                                ->label('')
                                                                ->markdown()
                                                                ->prose(),
                                                        ])
                                                        ->compact()
                                                        ->collapsible(),
                                                ]),
                                        ])->columnSpan(9),

                                        \Filament\Schemas\Components\Group::make([
                                            \Filament\Schemas\Components\Section::make(__('messages.pricing_inventory'))
                                                ->schema([
                                                    TextEntry::make('price')
                                                        ->label(__('messages.price'))
                                                        ->weight('bold')
                                                        ->size('lg')
                                                        ->money('SAR')
                                                        ->color('success'),
                                                    TextEntry::make('stock')
                                                        ->label(__('messages.stock'))
                                                        ->badge(),
                                                    IconEntry::make('is_featured')
                                                        ->label(__('messages.is_featured'))
                                                        ->boolean(),
                                                    TextEntry::make('status')
                                                        ->label(__('messages.status'))
                                                        ->badge()
                                                        ->color(fn (string $state): string => match ($state) {
                                                            'active' => 'success',
                                                            'inactive' => 'danger',
                                                            default => 'gray',
                                                        }),
                                                ]),
                                        ])->columnSpan(3),
                                    ]),
                            ]),

                        \Filament\Schemas\Components\Tabs\Tab::make(__('messages.specifications'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                \Filament\Infolists\Components\RepeatableEntry::make('specifications')
                                    ->label(__('messages.specifications'))
                                    ->schema([
                                        \Filament\Schemas\Components\Grid::make(3)
                                            ->schema([
                                                TextEntry::make('key_ar')
                                                    ->label(__('messages.key_ar'))
                                                    ->weight('bold'),
                                                TextEntry::make('value_ar')
                                                    ->label(__('messages.value_ar')),
                                                ImageEntry::make('icon')
                                                    ->label(__('messages.icon'))
                                                    ->disk('public')
                                                    ->circular(),
                                            ]),
                                    ])
                                    ->columns(1),
                            ]),

                        \Filament\Schemas\Components\Tabs\Tab::make(__('messages.gallery'))
                            ->icon('heroicon-o-photo')
                            ->schema([
                                \Filament\Infolists\Components\RepeatableEntry::make('images')
                                    ->label(__('messages.gallery'))
                                    ->schema([
                                        ImageEntry::make('images')
                                            ->label('')
                                            ->disk('public')
                                            ->width('100%')
                                            ->height('auto')
                                            ->extraImgAttributes(['class' => 'rounded-lg shadow-sm']),
                                    ])
                                    ->grid(4),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
