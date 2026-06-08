<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\ActivityLog;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('log_name')
                    ->label(__('messages.log_name'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('event')
                    ->label(__('messages.event'))
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'created' => __('messages.event_created'),
                        'updated' => __('messages.event_updated'),
                        'deleted' => __('messages.event_deleted'),
                        default => $state ?? '',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label(__('messages.description'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('subject_type')
                    ->label(__('messages.subject_type'))
                    ->formatStateUsing(fn (string $state) => class_basename($state))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subject_id')
                    ->label(__('messages.subject_id'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('causer.full_name')
                    ->label(__('messages.causer'))
                    ->placeholder('System')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->label(__('messages.event'))
                    ->options([
                        'created' => __('messages.event_created'),
                        'updated' => __('messages.event_updated'),
                        'deleted' => __('messages.event_deleted'),
                    ]),
                SelectFilter::make('log_name')
                    ->label(__('messages.log_name'))
                    ->options(fn () => ActivityLog::distinct()->pluck('log_name', 'log_name')->toArray()),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }
}
