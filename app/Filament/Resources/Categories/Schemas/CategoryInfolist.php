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

                // ── Hero Section: صورة القسم + المعلومات الأساسية ──
                Section::make()
                    ->schema([
                        Grid::make(12)
                            ->schema([

                                // صورة القسم
                                Group::make([
                                    ImageEntry::make('image')
                                        ->label('')
                                        ->disk('public')
                                        ->height(180)
                                        ->extraImgAttributes([
                                            'class' => 'rounded-2xl shadow-xl object-cover w-full',
                                            'style' => 'aspect-ratio:1/1;',
                                        ])
                                        ->columnSpanFull(),
                                ])->columnSpan(3),

                                // بيانات القسم
                                Group::make([
                                    TextEntry::make('name_ar')
                                        ->label(__('messages.service_ar'))
                                        ->weight('bold')
                                        ->size('lg')
                                        ->color('primary'),

                                    TextEntry::make('name_en')
                                        ->label(__('messages.service_en'))
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
                                        ->dateTime('d M Y')
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
                                Grid::make(12)
                                    ->schema([

                                        // صورة القسم الفرعي
                                        ImageEntry::make('image')
                                            ->label('')
                                            ->disk('public')
                                            ->circular()
                                            ->height(64)
                                            ->extraImgAttributes([
                                                'class' => 'ring-2 ring-primary-500 shadow',
                                            ])
                                            ->columnSpan(2),

                                        // معلومات القسم الفرعي
                                        Group::make([
                                            TextEntry::make('name_ar')
                                                ->label(__('messages.service_ar'))
                                                ->weight('bold')
                                                ->color('primary'),

                                            TextEntry::make('name_en')
                                                ->label(__('messages.service_en'))
                                                ->color('gray'),
                                        ])->columnSpan(8),

                                        // الحالة
                                        Group::make([
                                            TextEntry::make('status')
                                                ->label(__('messages.status'))
                                                ->badge()
                                                ->color(fn (string $state): string => match ($state) {
                                                    'active'   => 'success',
                                                    'inactive' => 'danger',
                                                    default    => 'gray',
                                                })
                                                ->formatStateUsing(fn (string $state): string => __("messages.{$state}")),
                                        ])->columnSpan(2),
                                    ]),
                            ])
                            ->grid(2)
                            ->placeholder(__('messages.no_sub_categories')),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }
}
