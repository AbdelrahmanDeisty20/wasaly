<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ReviewInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('product_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('provider_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('rating')
                    ->numeric(),
                TextEntry::make('comment')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('service_id')
                    ->numeric()
                    ->placeholder('-'),
            ]);
    }
}
