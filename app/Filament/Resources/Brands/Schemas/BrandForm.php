<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Section::make(__('messages.brand_details'))
                    ->schema([
                        \Filament\Forms\Components\Grid::make(2)
                            ->schema([
                                TextInput::make('name_ar')
                                    ->label(__('messages.service_ar_required'))
                                    ->required(),
                                TextInput::make('name_en')
                                    ->label(__('messages.service_en_required'))
                                    ->required(),
                            ]),
                        FileUpload::make('image')
                            ->label(__('messages.avatar_required'))
                            ->image()
                            ->directory('brands')
                            ->required(),
                        Select::make('status')
                            ->label(__('messages.status_required'))
                            ->options([
                                'active' => __('messages.active'),
                                'inactive' => __('messages.inactive')
                            ])
                            ->default('active')
                            ->required(),
                    ])
            ]);
    }
}
