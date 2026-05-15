<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
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
                                    ->columnSpan(3)
                                    ->extraImgAttributes(['class' => 'rounded-xl shadow-md']),

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

                // ── الأقسام الفرعية ──
                Section::make(__('messages.sub_categories'))
                    ->icon('heroicon-o-tag')
                    ->schema([
                        RepeatableEntry::make('subCategories')
                            ->label('')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        ImageEntry::make('image')
                                            ->label(__('messages.image'))
                                            ->disk('public')
                                            ->circular(),

                                        TextEntry::make('name_ar')
                                            ->label(__('messages.service_ar'))
                                            ->weight('bold'),

                                        TextEntry::make('name_en')
                                            ->label(__('messages.service_en')),

                                        TextEntry::make('status')
                                            ->label(__('messages.status'))
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'active'   => 'success',
                                                'inactive' => 'danger',
                                                default    => 'gray',
                                            })
                                            ->formatStateUsing(fn (string $state): string => __("messages.{$state}")),
                                    ]),
                            ])
                            ->columns(1)
                            ->placeholder('لا توجد أقسام فرعية حالياً'),
                    ])
                    ->collapsible(),
            ]);
    }
}
