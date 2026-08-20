<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BannerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $isAr = app()->getLocale() === 'ar';

        return $schema
            ->components([
                Section::make(__('messages.banner_details'))
                    ->schema([
                        Grid::make(12)
                            ->schema([
                                ImageEntry::make('image')
                                    ->label('')
                                    ->state(fn ($record) => $record->image_path)
                                    ->columnSpan(4)
                                    ->extraImgAttributes(['class' => 'rounded-lg shadow-md max-h-48 object-cover']),
                                
                                Group::make([
                                    TextEntry::make('title_display')
                                        ->label(__('messages.title'))
                                        ->state(fn ($record) => $isAr ? ($record->title_ar ?: $record->title_en) : ($record->title_en ?: $record->title_ar))
                                        ->weight('bold')
                                        ->size('lg')
                                        ->color('primary'),

                                    TextEntry::make('desc_display')
                                        ->label(__('messages.description'))
                                        ->state(fn ($record) => $isAr ? ($record->desc_ar ?: $record->desc_en) : ($record->desc_en ?: $record->desc_ar))
                                        ->color('gray'),

                                    TextEntry::make('type')
                                        ->label(__('messages.banner_type'))
                                        ->badge()
                                        ->color('info')
                                        ->formatStateUsing(fn (string $state): string => match ($state) {
                                            'home_page' => __('messages.home_page'),
                                            'product_page' => __('messages.product_page'),
                                            'coupon_page' => __('messages.coupon_page'),
                                            default => $state,
                                        }),

                                    TextEntry::make('link')
                                        ->label(__('messages.link'))
                                        ->placeholder('-'),

                                    TextEntry::make('status')
                                        ->label(__('messages.status'))
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            'active' => 'success',
                                            'inactive' => 'danger',
                                            default => 'gray',
                                        })
                                        ->formatStateUsing(fn (string $state): string => __("messages.{$state}")),

                                    TextEntry::make('created_at')
                                        ->label(__('messages.created_at'))
                                        ->dateTime()
                                        ->color('gray'),
                                ])->columnSpan(8),
                            ]),
                    ]),
            ]);
    }
}
