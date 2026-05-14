<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_ar')
                    ->label(__('messages.service_ar'))
                    ->searchable(),
                ImageColumn::make('image')
                    ->label(__('messages.image'))
                    ->circular(),
                TextColumn::make('price')
                    ->label(__('messages.price'))
                    ->money('SAR')
                    ->sortable(),
                TextColumn::make('stock')
                    ->label(__('messages.stock'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('subCategory.name_ar')
                    ->label(__('messages.sub_category'))
                    ->sortable(),
                TextColumn::make('brand.name_ar')
                    ->label(__('messages.brand'))
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('messages.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => __("messages.{$state}")),
                IconColumn::make('is_featured')
                    ->label(__('messages.is_featured'))
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
