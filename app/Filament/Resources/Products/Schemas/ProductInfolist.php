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
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        \Filament\Schemas\Components\Section::make()
                                            ->schema([
                                                TextEntry::make('name_ar')
                                                    ->label(__('messages.service_ar')),
                                                TextEntry::make('name_en')
                                                    ->label(__('messages.service_en')),
                                                TextEntry::make('description_ar')
                                                    ->label(__('messages.description_ar'))
                                                    ->columnSpanFull(),
                                                TextEntry::make('description_en')
                                                    ->label(__('messages.description_en'))
                                                    ->columnSpanFull(),
                                            ])
                                            ->columnSpan(2),

                                        \Filament\Schemas\Components\Group::make([
                                            \Filament\Schemas\Components\Section::make(__('messages.pricing_inventory'))
                                                ->schema([
                                                    TextEntry::make('price')
                                                        ->label(__('messages.price'))
                                                        ->money('SAR'),
                                                    TextEntry::make('stock')
                                                        ->label(__('messages.stock')),
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

                                            \Filament\Schemas\Components\Section::make(__('messages.associations'))
                                                ->schema([
                                                    TextEntry::make('subCategory.name_ar')
                                                        ->label(__('messages.sub_category')),
                                                    TextEntry::make('brand.name_ar')
                                                        ->label(__('messages.brand'))
                                                        ->placeholder('-'),
                                                ]),
                                            
                                            \Filament\Schemas\Components\Section::make(__('messages.image'))
                                                ->schema([
                                                    ImageEntry::make('image')
                                                        ->label('')
                                                        ->circular(),
                                                ]),
                                        ])->columnSpan(1),
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
                                                    ->label(__('messages.key_ar')),
                                                TextEntry::make('value_ar')
                                                    ->label(__('messages.value_ar')),
                                                ImageEntry::make('icon')
                                                    ->label(__('messages.icon'))
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
                                            ->width(200)
                                            ->height(200),
                                    ])
                                    ->grid(4),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
