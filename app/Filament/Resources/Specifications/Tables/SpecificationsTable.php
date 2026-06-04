<?php

namespace App\Filament\Resources\Specifications\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SpecificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('icon')
                    ->label(__('messages.icon'))
                    ->state(fn ($record) => $record->icon_path)
                    ->circular(),

                TextColumn::make('product.name_ar')
                    ->label(__('messages.product'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('key_ar')
                    ->label(__('messages.key_ar'))
                    ->searchable(),

                TextColumn::make('value_ar')
                    ->label(__('messages.value_ar'))
                    ->searchable(),

                TextColumn::make('key_en')
                    ->label(__('messages.key_en'))
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('value_en')
                    ->label(__('messages.value_en'))
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('product_id')
                    ->label(__('messages.product'))
                    ->relationship('product', 'name_ar')
                    ->searchable()
                    ->preload(),
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
