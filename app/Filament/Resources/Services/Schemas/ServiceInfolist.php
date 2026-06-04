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
                \Filament\Schemas\Components\Tabs::make('Service View')
                    ->tabs([
                        \Filament\Schemas\Components\Tabs\Tab::make(__('messages.service_details') ?? 'Service Details')
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
                                                                ->state(fn ($record) => $record->image_path)
                                                                ->columnSpan(3)
                                                                ->circular()
                                                                ->size(100)
                                                                ->extraImgAttributes(['class' => 'shadow-md']),
                                                            
                                                            \Filament\Schemas\Components\Group::make([
                                                                TextEntry::make('service_ar')
                                                                    ->label('')
                                                                    ->weight('bold')
                                                                    ->size('lg')
                                                                    ->color('primary'),
                                                                TextEntry::make('service_en')
                                                                    ->label('')
                                                                    ->size('md')
                                                                    ->color('gray'),
                                                                
                                                                \Filament\Schemas\Components\Grid::make(2)
                                                                    ->schema([
                                                                        TextEntry::make('provider.title_ar')
                                                                            ->label(__('messages.service_provider'))
                                                                            ->icon('heroicon-m-user-group'),
                                                                        TextEntry::make('subCategory.name_ar')
                                                                            ->label(__('messages.sub_category'))
                                                                            ->icon('heroicon-m-tag'),
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
                                            \Filament\Schemas\Components\Section::make(__('messages.pricing') ?? 'Pricing')
                                                ->schema([
                                                    TextEntry::make('price')
                                                        ->label(__('messages.price'))
                                                        ->weight('bold')
                                                        ->size('lg')
                                                        ->money('EGP')
                                                        ->color('success'),
                                                ]),
                                        ])->columnSpan(3),
                                    ]),
                            ]),

                        \Filament\Schemas\Components\Tabs\Tab::make(__('messages.gallery'))
                            ->icon('heroicon-o-photo')
                            ->schema([
                                \Filament\Infolists\Components\RepeatableEntry::make('serviceImages')
                                    ->label(__('messages.gallery'))
                                    ->schema([
                                        ImageEntry::make('images')
                                            ->label('')
                                            ->state(fn ($record) => $record->images_path)
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
