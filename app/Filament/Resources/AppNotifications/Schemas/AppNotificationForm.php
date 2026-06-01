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
                TextInput::make('title_ar')
                    ->label(__('messages.title_ar'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('title_en')
                    ->label(__('messages.title_en'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('type')
                    ->label(__('messages.type'))
                    ->default('general')
                    ->required()
                    ->maxLength(255),
                Textarea::make('message_ar')
                    ->label(__('messages.message_ar'))
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('message_en')
                    ->label(__('messages.message_en'))
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('is_read')
                    ->label(__('messages.is_read'))
                    ->default(false)
                    ->required(),
            ]);
    }
}
