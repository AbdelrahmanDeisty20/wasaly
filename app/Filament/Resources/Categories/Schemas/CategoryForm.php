<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Section::make(__('messages.category_details'))
                    ->schema([
                        \Filament\Forms\Components\Grid::make(2)
                            ->schema([
                                TextInput::make('name_ar')
                                    ->label(__('messages.service_ar'))
                                    ->placeholder('مثال: مطاعم')
                                    ->required(),
                                TextInput::make('name_en')
                                    ->label(__('messages.service_en'))
                                    ->placeholder('e.g. Restaurants')
                                    ->required(),
                                Select::make('status')
                                    ->label(__('messages.status'))
                                    ->options([
                                        'active' => __('messages.active'),
                                        'inactive' => __('messages.inactive')
                                    ])
                                    ->default('active')
                                    ->required(),
                                FileUpload::make('image')
                                    ->label(__('messages.image'))
                                    ->image()
                                    ->directory('categories')
                                    ->required(),
                            ]),
                    ])
            ]);
    }
}
