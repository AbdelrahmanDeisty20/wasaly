<?php

namespace App\Filament\Resources\SubCategories\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SubCategoryInfolist
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
                                    ->state(function ($record) {
                                        if (!$record->image) return null;
                                        return str_starts_with($record->image, 'subcategories/') ? $record->image : 'subcategories/' . $record->image;
                                    })
                                    ->columnSpan(3)
                                    ->circular()
                                    ->size(100)
                                    ->extraImgAttributes(['class' => 'shadow-md']),

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

                                    \Filament\Schemas\Components\Grid::make(2)->schema([
                                        TextEntry::make('status')
                                            ->label(__('messages.status'))
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'active'   => 'success',
                                                'inactive' => 'danger',
                                                default    => 'gray',
                                            })
                                            ->formatStateUsing(fn (string $state): string => __("messages.{$state}")),

                                        TextEntry::make('category.name_ar')
                                            ->label(__('messages.category') ?? 'Category')
                                            ->badge()
                                            ->color('info'),
                                    ]),

                                    TextEntry::make('created_at')
                                        ->label(__('messages.created_at'))
                                        ->dateTime()
                                        ->color('gray'),

                                ])->columnSpan(9),
                            ]),
                    ]),
            ]);
    }
}
