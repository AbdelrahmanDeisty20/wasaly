<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name_ar'),
                TextEntry::make('name_en'),
                TextEntry::make('description_ar')
                    ->columnSpanFull(),
                TextEntry::make('description_en')
                    ->columnSpanFull(),
                TextEntry::make('price'),
                TextEntry::make('stock')
                    ->numeric(),
                ImageEntry::make('image'),
                TextEntry::make('status')
                    ->badge(),
                IconEntry::make('is_featured')
                    ->boolean(),
                TextEntry::make('sub_category_id')
                    ->numeric(),
                TextEntry::make('brand_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
