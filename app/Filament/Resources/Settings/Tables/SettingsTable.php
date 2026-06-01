<?php

namespace App\Filament\Resources\Settings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key_ar')
                    ->label(__('messages.key') . ' (العربية)')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('key_en')
                    ->label(__('messages.key') . ' (English)')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('value_ar')
                    ->label(__('messages.value') . ' (العربية)')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('value_en')
                    ->label(__('messages.value') . ' (English)')
                    ->limit(50)
                    ->searchable(),
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
