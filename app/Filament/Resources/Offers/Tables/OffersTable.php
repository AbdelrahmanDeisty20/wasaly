<?php

namespace App\Filament\Resources\Offers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OffersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name_ar')
                    ->label(__('messages.product'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('discount_percentage')
                    ->label(__('messages.discount_percentage'))
                    ->badge()
                    ->color('success')
                    ->suffix('%')
                    ->sortable(),

                TextColumn::make('start_date')
                    ->label(__('messages.start_date'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label(__('messages.end_date'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('messages.active'))
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label(__('messages.status'))
                    ->options([
                        '1' => __('messages.active'),
                        '0' => __('messages.inactive'),
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
