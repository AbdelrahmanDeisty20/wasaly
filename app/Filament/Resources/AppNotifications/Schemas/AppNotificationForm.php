<?php

namespace App\Filament\Resources\AppNotifications\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AppNotificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label(__('messages.user'))
                    ->relationship('user', 'full_name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name ?? $record->email ?? 'User #' . $record->id)
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->placeholder(app()->getLocale() == 'ar' ? 'جميع المستخدمين (إرسال للكل)' : 'All Users (Send to all)'),
                TextInput::make('title')
                    ->label(__('messages.title'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('type')
                    ->label(__('messages.type'))
                    ->default('general')
                    ->required()
                    ->maxLength(255),
                Textarea::make('message')
                    ->label(__('messages.message'))
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('is_read')
                    ->label(__('messages.is_read'))
                    ->default(false)
                    ->required(),
            ]);
    }
}
