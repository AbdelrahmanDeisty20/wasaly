<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('messages.product_details'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('provider_id')
                                    ->label(__('messages.service_provider'))
                                    ->relationship('provider', 'title_ar')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('sub_category_id')
                                    ->label(__('messages.sub_category'))
                                    ->relationship('subCategory', 'name_ar')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('service_ar')
                                    ->label(__('messages.service_ar'))
                                    ->required(),
                                TextInput::make('service_en')
                                    ->label(__('messages.service_en'))
                                    ->required(),
                            ]),
                    ]),

                Section::make(__('messages.description_ar'))
                    ->schema([
                        Textarea::make('description_ar')
                            ->label('')
                            ->required(),
                        Textarea::make('description_en')
                            ->label(__('messages.description_en'))
                            ->required(),
                    ]),

                Section::make(__('messages.pricing_inventory'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('price')
                                    ->label(__('messages.price'))
                                    ->numeric()
                                    ->prefix('SAR')
                                    ->required(),
                                FileUpload::make('image')
                                    ->label(__('messages.image'))
                                    ->image()
                                    ->directory('services')
                                    ->required(),
                            ]),
                    ]),
            ]);
    }
}
