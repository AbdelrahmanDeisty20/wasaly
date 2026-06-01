<?php

namespace App\Filament\Resources\Specifications\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SpecificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('messages.specification_details'))
                    ->schema([
                        Select::make('product_id')
                            ->label(__('messages.product'))
                            ->relationship('product', 'name_ar')
                            ->getOptionLabelFromRecordUsing(fn (\App\Models\Product $record) =>
                                ($record->name_ar ?? $record->name_en ?? ('منتج #' . $record->id))
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('key_ar')
                                    ->label(__('messages.key_ar'))
                                    ->required(),
                                TextInput::make('key_en')
                                    ->label(__('messages.key_en'))
                                    ->required(),
                                TextInput::make('value_ar')
                                    ->label(__('messages.value_ar'))
                                    ->required(),
                                TextInput::make('value_en')
                                    ->label(__('messages.value_en'))
                                    ->required(),
                            ]),

                        FileUpload::make('icon')
                            ->label(__('messages.icon'))
                            ->image()
                            ->disk('public')
                            ->directory('specifications')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
