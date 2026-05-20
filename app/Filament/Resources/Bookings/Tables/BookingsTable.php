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
                TextColumn::make('service.service_ar')
                    ->label(__('messages.service_ar'))
                    ->searchable(),
                TextColumn::make('provider.title_ar')
                    ->label(__('messages.service_provider'))
                    ->badge()
                    ->color('gray'),
                TextColumn::make('date')
                    ->label(__('messages.date_required'))
                    ->date('d M Y')
                    ->sortable(),
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
                    ->label(app()->getLocale() == 'ar' ? 'العميل' : 'Customer')
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
