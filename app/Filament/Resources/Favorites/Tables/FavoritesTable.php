<?php

namespace App\Filament\Resources\Favorites\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FavoritesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(app()->getLocale() == 'ar' ? 'المستخدم' : 'User')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('type')
                    ->label(app()->getLocale() == 'ar' ? 'النوع' : 'Type')
                    ->getStateUsing(function ($record) {
                        if ($record->product_id) return app()->getLocale() == 'ar' ? 'منتج: ' . $record->product->name_ar : 'Product: ' . $record->product->name_en;
                        if ($record->service_id) return app()->getLocale() == 'ar' ? 'خدمة: ' . $record->service->service_ar : 'Service: ' . $record->service->service_en;
                        if ($record->provider_id) return app()->getLocale() == 'ar' ? 'مقدم خدمة: ' . $record->provider->title_ar : 'Provider: ' . $record->provider->title_en;
                        return app()->getLocale() == 'ar' ? 'غير معروف' : 'Unknown';
                    })
                    ->badge()
                    ->color('danger')
                    ->icon('heroicon-s-heart'),
                IconColumn::make('is_active')
                    ->label(__('messages.active'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('favorite_type')
                    ->label(app()->getLocale() == 'ar' ? 'نوع المفضلة' : 'Favorite Type')
                    ->options([
                        'product' => app()->getLocale() == 'ar' ? 'المنتجات' : 'Products',
                        'service' => app()->getLocale() == 'ar' ? 'الخدمات' : 'Services',
                        'provider' => app()->getLocale() == 'ar' ? 'مقدمي الخدمة' : 'Providers',
                    ])
                    ->query(function ($query, array $data) {
                        return match ($data['value'] ?? null) {
                            'product' => $query->whereNotNull('product_id'),
                            'service' => $query->whereNotNull('service_id'),
                            'provider' => $query->whereNotNull('provider_id'),
                            default => $query,
                        };
                    }),
                \Filament\Tables\Filters\SelectFilter::make('user_id')
                    ->label(app()->getLocale() == 'ar' ? 'المستخدم' : 'User')
                    ->relationship('user', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name ?? $record->email ?? 'User #' . $record->id)
                    ->searchable()
                    ->preload(),
                \Filament\Tables\Filters\SelectFilter::make('provider_id')
                    ->label(app()->getLocale() == 'ar' ? 'مقدم الخدمة' : 'Provider')
                    ->relationship('provider', 'title_ar')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->title_ar ?? $record->title_en ?? 'Provider #' . $record->id)
                    ->searchable()
                    ->preload(),
                \Filament\Tables\Filters\SelectFilter::make('service_id')
                    ->label(app()->getLocale() == 'ar' ? 'الخدمة' : 'Service')
                    ->relationship('service', 'service_ar')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->service_ar ?? $record->service_en ?? 'Service #' . $record->id)
                    ->searchable()
                    ->preload(),
                \Filament\Tables\Filters\SelectFilter::make('product_id')
                    ->label(app()->getLocale() == 'ar' ? 'المنتج' : 'Product')
                    ->relationship('product', 'name_ar')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name_ar ?? $record->name_en ?? 'Product #' . $record->id)
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
