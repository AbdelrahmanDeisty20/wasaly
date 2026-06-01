<?php

namespace App\Filament\Resources\Offers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class OfferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('messages.offer_details'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('product_id')
                                    ->label(__('messages.product'))
                                    ->relationship('product', 'name_ar')
                                    ->getOptionLabelFromRecordUsing(fn (\App\Models\Product $record) =>
                                        ($record->name_ar ?? $record->name_en ?? ('منتج #' . $record->id))
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                TextInput::make('discount_percentage')
                                    ->label(__('messages.discount_percentage'))
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(100)
                                    ->suffix('%')
                                    ->required(),

                                DateTimePicker::make('start_date')
                                    ->label(__('messages.start_date'))
                                    ->required(),

                                DateTimePicker::make('end_date')
                                    ->label(__('messages.end_date'))
                                    ->required()
                                    ->after('start_date'),

                                Toggle::make('is_active')
                                    ->label(__('messages.active_question'))
                                    ->default(true)
                                    ->onColor('success')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
