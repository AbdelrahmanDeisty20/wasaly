<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key_ar')
                    ->label(__('messages.key') . ' (العربية)')
                    ->required()
                    ->maxLength(255),
                TextInput::make('key_en')
                    ->label(__('messages.key') . ' (English)')
                    ->required()
                    ->maxLength(255),
                Textarea::make('value_ar')
                    ->label(__('messages.value') . ' (العربية)')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('value_en')
                    ->label(__('messages.value') . ' (English)')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
