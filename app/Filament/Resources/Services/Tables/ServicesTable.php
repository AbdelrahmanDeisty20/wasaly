<?php

namespace App\Filament\Resources\Services\Tables;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label(__('messages.image'))
                    ->disk('public')
                    ->circular(),
                TextColumn::make('service_ar')
                    ->label(__('messages.service_ar'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('provider.title_ar')
                    ->label(__('messages.service_provider'))
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('subCategory.name_ar')
                    ->label(__('messages.sub_category'))
                    ->badge()
                    ->color('info'),
                TextColumn::make('price')
                    ->label(__('messages.price'))
                    ->money('SAR')
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),
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
