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
                \Filament\Schemas\Components\Section::make()
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(3)
                            ->schema([
                                TextEntry::make('order_number')
                                    ->label(__('messages.order_number'))
                                    ->weight('bold')
                                    ->size('lg')
                                    ->color('primary')
                                    ->icon('heroicon-m-hashtag'),
                                
                                TextEntry::make('status')
                                    ->label(__('messages.status'))
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'completed', 'delivered' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'gray',
                                    })
                                    ->alignCenter(),
                                
                                TextEntry::make('total_price')
                                    ->label(__('messages.total_price'))
                                    ->weight('bold')
                                    ->size('lg')
                                    ->money('SAR')
                                    ->color('success')
                                    ->alignEnd(),
                            ]),
                    ])->compact(),

                \Filament\Schemas\Components\Grid::make(2)
                    ->schema([
                        \Filament\Schemas\Components\Section::make(__('messages.customer_details'))
                            ->icon('heroicon-o-user')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(2)
                                    ->schema([
                                        TextEntry::make('customer_name')
                                            ->label(__('messages.customer_name'))
                                            ->weight('bold')
                                            ->columnSpanFull(),
                                        TextEntry::make('customer_phone')
                                            ->label(__('messages.phone'))
                                            ->icon('heroicon-m-phone'),
                                        TextEntry::make('user.email')
                                            ->label('البريد الإلكتروني')
                                            ->icon('heroicon-m-envelope')
                                            ->placeholder('-'),
                                    ])
                            ])
                            ->columnSpan(1)
                            ->extraAttributes(['class' => 'h-full']),

                        \Filament\Schemas\Components\Section::make('معلومات الشحن والتوصيل')
                            ->icon('heroicon-o-truck')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(2)
                                    ->schema([
                                        TextEntry::make('governorate.name_ar')
                                            ->label('المحافظة'),
                                        TextEntry::make('center.name_ar')
                                            ->label('المركز/المنطقة'),
                                        TextEntry::make('customer_address')
                                            ->label('العنوان بالتفصيل')
                                            ->columnSpanFull(),
                                        TextEntry::make('shipping_cost')
                                            ->label('تكلفة الشحن')
                                            ->money('SAR')
                                            ->color('gray'),
                                    ])
                            ])
                            ->columnSpan(1)
                            ->extraAttributes(['class' => 'h-full']),
                    ])->extraAttributes(['class' => 'items-stretch']),

                \Filament\Schemas\Components\Section::make('منتجات الطلب')
                    ->icon('heroicon-o-shopping-bag')
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(4)
                                    ->schema([
                                        TextEntry::make('product.name_ar')
                                            ->label('المنتج')
                                            ->weight('bold'),
                                        TextEntry::make('unit_price')
                                            ->label('سعر الوحدة')
                                            ->money('SAR'),
                                        TextEntry::make('quantity')
                                            ->label('الكمية')
                                            ->badge(),
                                        TextEntry::make('total_price')
                                            ->label('الإجمالي')
                                            ->money('SAR')
                                            ->weight('bold')
                                            ->color('primary'),
                                    ]),
                            ])
                            ->columns(1)
                            ->placeholder('لا توجد منتجات مسجلة لهذا الطلب'),
                    ]),

                \Filament\Schemas\Components\Section::make('ملخص الطلب والدفع')
                    ->icon('heroicon-o-credit-card')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(4)
                            ->schema([
                                TextEntry::make('payment_method')
                                    ->label('طريقة الدفع')
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('coupon_code')
                                    ->label('كوبون الخصم')
                                    ->placeholder('لا يوجد')
                                    ->icon('heroicon-m-ticket'),
                                TextEntry::make('discount_amount')
                                    ->label('قيمة الخصم')
                                    ->money('SAR')
                                    ->color('danger'),
                                TextEntry::make('total_price')
                                    ->label('الإجمالي النهائي')
                                    ->money('SAR')
                                    ->weight('bold')
                                    ->size('md'),
                            ]),
                    ])->compact(),
            ]);
    }
}
