<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\ImageColumn::make('avatar')
                    ->label(__('messages.avatar_required'))
                    ->disk('public')
                    ->state(fn ($record) => $record->avatar ? 'avatars/' . $record->avatar : null)
                    ->circular(),
                TextColumn::make('name')
                    ->label(__('messages.user'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('messages.email_nullable'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(__('messages.phone_nullable'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(__('messages.type_required'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'user' => 'success',
                        'service_provider' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => __("messages.{$state}")),
                IconColumn::make('is_active')
                    ->label(__('messages.active'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
