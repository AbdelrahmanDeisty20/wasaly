<?php

namespace App\Filament\Resources\Banners\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BannersTable
{
    public static function configure(Table $table): Table
    {
        $isAr = app()->getLocale() === 'ar';

        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label(__('messages.image'))
                    ->state(fn ($record) => $record->image_path)
                    ->square(),

                TextColumn::make('title_display')
                    ->label(__('messages.title'))
                    ->state(fn ($record) => $isAr ? ($record->title_ar ?: $record->title_en) : ($record->title_en ?: $record->title_ar))
                    ->searchable(query: function ($query, $search) {
                        $query->where('title_ar', 'like', "%{$search}%")
                              ->orWhere('title_en', 'like', "%{$search}%");
                    })
                    ->sortable(),

                TextColumn::make('type')
                    ->label(__('messages.banner_type'))
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'home_page' => __('messages.home_page'),
                        'product_page' => __('messages.product_page'),
                        'coupon_page' => __('messages.coupon_page'),
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('link')
                    ->label(__('messages.link'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->label(__('messages.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => __("messages.{$state}"))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('messages.banner_type'))
                    ->options([
                        'home_page' => __('messages.home_page'),
                        'product_page' => __('messages.product_page'),
                        'coupon_page' => __('messages.coupon_page'),
                    ]),
                SelectFilter::make('status')
                    ->label(__('messages.status'))
                    ->options([
                        'active' => __('messages.active'),
                        'inactive' => __('messages.inactive'),
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
