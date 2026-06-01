<?php

namespace App\Filament\Resources\AppNotifications\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;

class AppNotificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.full_name')
                    ->label(__('messages.user'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->placeholder(app()->getLocale() == 'ar' ? 'جميع المستخدمين' : 'All Users'),
                TextColumn::make('title')
                    ->label(__('messages.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('messages.type'))
                    ->badge()
                    ->color('info')
                    ->sortable(),
                IconColumn::make('is_read')
                    ->label(__('messages.is_read'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('user_id')
                    ->label(__('messages.user'))
                    ->relationship('user', 'full_name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name ?? $record->email ?? 'User #' . $record->id)
                    ->searchable()
                    ->preload(),
                \Filament\Tables\Filters\TernaryFilter::make('is_read')
                    ->label(__('messages.is_read'))
                    ->trueLabel(__('messages.read'))
                    ->falseLabel(__('messages.unread')),
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
