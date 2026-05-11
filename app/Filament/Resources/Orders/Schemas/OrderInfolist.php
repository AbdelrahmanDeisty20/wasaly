<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('order_number'),
                TextEntry::make('user_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('address_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('governorate_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('shipping_cost')
                    ->money(),
                TextEntry::make('center_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('coupon_code')
                    ->placeholder('-'),
                TextEntry::make('unit_price')
                    ->money(),
                TextEntry::make('discount_amount')
                    ->numeric(),
                TextEntry::make('quantity')
                    ->numeric(),
                TextEntry::make('total_price')
                    ->money(),
                TextEntry::make('customer_name'),
                TextEntry::make('customer_phone'),
                TextEntry::make('customer_address')
                    ->placeholder('-'),
                TextEntry::make('region')
                    ->placeholder('-'),
                TextEntry::make('payment_method')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
