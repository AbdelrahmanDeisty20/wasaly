<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('messages.order_info'))
                                    ->schema([
                                        TextInput::make('order_number')
                                            ->label(__('messages.order_number'))
                                            ->disabled()
                                            ->dehydrated(false),
                                        Select::make('status')
                                            ->label(__('messages.status_required'))
                                            ->options([
                                                'pending' => __('messages.pending'),
                                                'accepted' => __('messages.accepted'),
                                                'processing' => __('messages.processing'),
                                                'shipped' => __('messages.shipped'),
                                                'delivered' => __('messages.delivered'),
                                                'cancelled' => __('messages.cancelled'),
                                            ])
                                            ->required()
                                            ->native(false),
                                        Select::make('payment_method')
                                            ->label(__('messages.payment_method'))
                                            ->options([
                                                'cash' => __('messages.cash'),
                                                'card' => __('messages.card'),
                                            ])
                                            ->disabled(),
                                    ]),
                                
                                Section::make(__('messages.customer_details'))
                                    ->schema([
                                        TextInput::make('customer_name')
                                            ->label(__('messages.customer_name_required'))
                                            ->required(),
                                        TextInput::make('customer_phone')
                                            ->label(__('messages.customer_phone_required'))
                                            ->tel()
                                            ->required(),
                                        TextInput::make('customer_address')
                                            ->label(__('messages.delivery_address_required')),
                                    ]),
                            ])
                            ->columnSpan(2),
                        
                        Section::make(__('messages.financials'))
                            ->schema([
                                TextInput::make('total_price')
                                    ->label(__('messages.total_price'))
                                    ->numeric()
                                    ->prefix('SAR')
                                    ->disabled(),
                                TextInput::make('shipping_cost')
                                    ->label(__('messages.shipping_cost'))
                                    ->numeric()
                                    ->prefix('SAR'),
                                TextInput::make('discount_amount')
                                    ->label(__('messages.discount_amount'))
                                    ->numeric()
                                    ->prefix('SAR'),
                                TextInput::make('coupon_code')
                                    ->label(__('messages.coupon_code'))
                                    ->disabled(),
                            ])
                            ->columnSpan(1),
                    ])
            ]);
    }
}
