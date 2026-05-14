<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Grid::make(3)
                    ->schema([
                        \Filament\Schemas\Components\Section::make(__('messages.product_details'))
                            ->schema([
                                TextInput::make('name_ar')
                                    ->label(__('messages.service_ar'))
                                    ->required(),
                                TextInput::make('name_en')
                                    ->label(__('messages.service_en'))
                                    ->required(),
                                Textarea::make('description_ar')
                                    ->label(__('messages.description_ar'))
                                    ->required()
                                    ->rows(3),
                                Textarea::make('description_en')
                                    ->label(__('messages.description_en'))
                                    ->required()
                                    ->rows(3),
                            ])
                            ->columnSpan(2),
                        
                        \Filament\Schemas\Components\Group::make()
                            ->schema([
                                \Filament\Schemas\Components\Section::make(__('messages.pricing_inventory'))
                                    ->schema([
                                        TextInput::make('price')
                                            ->label(__('messages.price'))
                                            ->numeric()
                                            ->prefix('SAR')
                                            ->required(),
                                        TextInput::make('stock')
                                            ->label(__('messages.stock'))
                                            ->numeric()
                                            ->default(1)
                                            ->required(),
                                        Toggle::make('is_featured')
                                            ->label(__('messages.is_featured'))
                                            ->onColor('success'),
                                        Select::make('status')
                                            ->label(__('messages.status'))
                                            ->options([
                                                'active' => __('messages.active'),
                                                'inactive' => __('messages.inactive')
                                            ])
                                            ->default('active')
                                            ->required(),
                                    ]),
                                
                                \Filament\Schemas\Components\Section::make(__('messages.associations'))
                                    ->schema([
                                        Select::make('sub_category_id')
                                            ->label(__('messages.sub_category'))
                                            ->relationship('subCategory', 'name_ar')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        Select::make('brand_id')
                                            ->label(__('messages.brand'))
                                            ->relationship('brand', 'name_ar')
                                            ->searchable()
                                            ->preload(),
                                    ]),
                            ])
                            ->columnSpan(1),
                    ]),
                
                \Filament\Schemas\Components\Section::make(__('messages.media'))
                    ->schema([
                        FileUpload::make('image')
                            ->label(__('messages.image'))
                            ->image()
                            ->directory('products')
                            ->required(),
                    ])
                    ->collapsible(),
            ]);
    }
}
