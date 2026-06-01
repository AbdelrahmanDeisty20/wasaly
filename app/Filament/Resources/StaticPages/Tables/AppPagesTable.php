<?php

namespace App\Filament\Resources\StaticPages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AppPagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title_ar')
                    ->label(__('messages.title_ar'))
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('title_en')
                    ->label(__('messages.title_en'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('status')
                    ->label(__('messages.active_question'))
                    ->badge()
                    ->color(fn ($state) => $state === 'active' ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state) => $state === 'active' ? __('messages.active') : __('messages.inactive'))
                    ->sortable(),
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
