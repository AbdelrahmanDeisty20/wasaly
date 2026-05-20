<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class CategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── معلومات القسم الرئيسي ──
                Section::make()
                    ->schema([
                        Grid::make(12)
                            ->schema([
                                ImageEntry::make('image')
                                    ->label('')
                                    ->disk('public')
                                    ->state(function ($record) {
                                        if (!$record->image) return null;
                                        return str_starts_with($record->image, 'categories/') ? $record->image : 'categories/' . $record->image;
                                    })
                                    ->columnSpan(3)
                                    ->circular()
                                    ->size(100)
                                    ->extraImgAttributes(['class' => 'shadow-md']),

                                Group::make([
                                    TextEntry::make('name_ar')
                                        ->label('')
                                        ->weight('bold')
                                        ->size('lg')
                                        ->color('primary'),

                                    TextEntry::make('name_en')
                                        ->label('')
                                        ->size('md')
                                        ->color('gray'),

                                    Grid::make(2)->schema([
                                        TextEntry::make('status')
                                            ->label(__('messages.status'))
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'active'   => 'success',
                                                'inactive' => 'danger',
                                                default    => 'gray',
                                            })
                                            ->formatStateUsing(fn (string $state): string => __("messages.{$state}")),

                                        TextEntry::make('sub_categories_count')
                                            ->label(__('messages.sub_categories'))
                                            ->badge()
                                            ->color('info')
                                            ->getStateUsing(fn ($record) => $record->subCategories()->count()),
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
