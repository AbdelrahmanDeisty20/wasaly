<?php

namespace App\Filament\Resources\StaticPages\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class AppPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title_ar')
                    ->label(__('messages.title_ar'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('title_en')
                    ->label(__('messages.title_en'))
                    ->required()
                    ->maxLength(255),
                RichEditor::make('content_ar')
                    ->label(__('messages.content_ar'))
                    ->required()
                    ->columnSpanFull(),
                RichEditor::make('content_en')
                    ->label(__('messages.content_en'))
                    ->required()
                    ->columnSpanFull(),
                Select::make('status')
                    ->label(__('messages.active_question'))
                    ->options([
                        'active' => __('messages.active') ?? 'Active',
                        'inactive' => __('messages.inactive') ?? 'Inactive',
                    ])
                    ->default('active')
                    ->required(),
            ]);
    }
}
