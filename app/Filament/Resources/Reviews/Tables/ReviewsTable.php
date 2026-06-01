<?php

namespace App\Filament\Resources\Reviews\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.full_name')
                    ->label(__('messages.user'))
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('rating')
                    ->label(__('messages.rating'))
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state >= 4 ? 'success' : ($state >= 3 ? 'warning' : 'danger'))
                    ->formatStateUsing(fn ($state) => $state . ' ⭐'),
                TextColumn::make('type')
                    ->label(__('messages.type'))
                    ->getStateUsing(function ($record) {
                        if ($record->product_id) return (app()->getLocale() == 'ar' ? 'منتج: ' : 'Product: ') . ($record->product->name_ar ?? $record->product->name_en);
                        if ($record->service_id) return (app()->getLocale() == 'ar' ? 'خدمة: ' : 'Service: ') . ($record->service->service_ar ?? $record->service->service_en);
                        if ($record->provider_id) return (app()->getLocale() == 'ar' ? 'مقدم خدمة: ' : 'Provider: ') . ($record->provider->title_ar ?? $record->provider->title_en);
                        return __('messages.general_app_review');
                    })
                    ->badge()
                    ->color('info'),
                TextColumn::make('comment')
                    ->label(__('messages.comment'))
                    ->limit(30)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('review_type')
                    ->label(__('messages.review_type'))
                    ->options([
                        'app' => __('messages.app_reviews'),
                        'product' => __('messages.product_reviews'),
                        'service' => __('messages.service_reviews'),
                        'provider' => __('messages.provider_reviews'),
                    ])
                    ->query(function ($query, array $data) {
                        return match ($data['value'] ?? null) {
                            'app' => $query->whereNull('product_id')->whereNull('service_id')->whereNull('provider_id'),
                            'product' => $query->whereNotNull('product_id'),
                            'service' => $query->whereNotNull('service_id'),
                            'provider' => $query->whereNotNull('provider_id'),
                            default => $query,
                        };
                    }),
                \Filament\Tables\Filters\SelectFilter::make('user_id')
                    ->label(__('messages.user'))
                    ->relationship('user', 'full_name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name ?? $record->email ?? 'User #' . $record->id)
                    ->searchable()
                    ->preload(),
                \Filament\Tables\Filters\SelectFilter::make('provider_id')
                    ->label(__('messages.optional_provider'))
                    ->relationship('provider', 'title_ar')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->title_ar ?? $record->title_en ?? 'Provider #' . $record->id)
                    ->searchable()
                    ->preload(),
                \Filament\Tables\Filters\SelectFilter::make('service_id')
                    ->label(__('messages.services'))
                    ->relationship('service', 'service_ar')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->service_ar ?? $record->service_en ?? 'Service #' . $record->id)
                    ->searchable()
                    ->preload(),
                \Filament\Tables\Filters\SelectFilter::make('product_id')
                    ->label(__('messages.product'))
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
