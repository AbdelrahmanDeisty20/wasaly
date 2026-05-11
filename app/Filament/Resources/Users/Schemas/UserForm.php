<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name'),
                TextInput::make('full_name'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('avatar'),
                Select::make('type')
                    ->options(['user' => 'User', 'service_provider' => 'Service provider'])
                    ->default('user')
                    ->required(),
                TextInput::make('provider'),
                TextInput::make('provider_id'),
                DateTimePicker::make('email_verified_at'),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('password')
                    ->password(),
            ]);
    }
}
