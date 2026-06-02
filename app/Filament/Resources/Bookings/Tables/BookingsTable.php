<?php

namespace App\Filament\Resources\Bookings\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        $isAr = app()->getLocale() == 'ar';

        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('messages.order_number'))
                    ->prefix('#')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('messages.user'))
                    ->searchable(),
                TextColumn::make('service_display')
                    ->label(__('messages.service_ar'))
                    ->state(fn ($record) => $isAr 
                        ? ($record->service?->service_ar ?: $record->service?->service_en) 
                        : ($record->service?->service_en ?: $record->service?->service_ar)
                    )
                    ->searchable(query: function ($query, $search) {
                        $query->whereHas('service', function ($q) use ($search) {
                            $q->where('service_ar', 'like', "%{$search}%")
                              ->orWhere('service_en', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('provider_display')
                    ->label(__('messages.service_provider'))
                    ->state(fn ($record) => $isAr 
                        ? ($record->provider?->title_ar ?: $record->provider?->title_en) 
                        : ($record->provider?->title_en ?: $record->provider?->title_ar)
                    )
                    ->searchable(query: function ($query, $search) {
                        $query->whereHas('provider', function ($q) use ($search) {
                            $q->where('title_ar', 'like', "%{$search}%")
                              ->orWhere('title_en', 'like', "%{$search}%");
                        });
                    })
                    ->badge()
                    ->color('gray'),
                TextColumn::make('availableDay.name_ar')
                    ->label($isAr ? 'اليوم' : 'Day')
                    ->getStateUsing(fn ($record) => 
                        $isAr 
                            ? ($record->availableDay?->name_ar ?? '-')
                            : ($record->availableDay?->name_en ?? '-')
                    ),
                TextColumn::make('status')
                    ->label(__('messages.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'accepted' => 'info',
                        'processing' => 'warning',
                        'shipped' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => __("messages.{$state}")),
                TextColumn::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('user_id')
                    ->label($isAr ? 'العميل' : 'Customer')
                    ->relationship('user', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name ?? $record->email ?? 'User #' . $record->id)
                    ->searchable()
                    ->preload(),
                \Filament\Tables\Filters\SelectFilter::make('provider_id')
                    ->label($isAr ? 'مقدم الخدمة' : 'Provider')
                    ->relationship('provider', 'title_ar')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->title_ar ?? $record->title_en ?? 'Provider #' . $record->id)
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
