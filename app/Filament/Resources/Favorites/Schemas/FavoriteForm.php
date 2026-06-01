<?php

namespace App\Filament\Resources\Favorites\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FavoriteForm
{
    public static function configure(Schema $schema): Schema
    {
        $isAr = app()->getLocale() == 'ar';
        return $schema
            ->components([
                Select::make('user_id')
                    ->label($isAr ? 'المستخدم' : 'User')
                    ->relationship('user', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name ?? $record->email ?? 'User #' . $record->id)
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('favorite_target')
                    ->label($isAr ? 'نوع المفضلة' : 'Favorite Type')
                    ->options([
                        'product' => $isAr ? 'منتج' : 'Product',
                        'service' => $isAr ? 'خدمة' : 'Service',
                        'provider' => $isAr ? 'مقدم خدمة' : 'Provider',
                    ])
                    ->reactive()
                    ->afterStateHydrated(function ($set, $record) {
                        if ($record) {
                            if ($record->product_id) $set('favorite_target', 'product');
                            elseif ($record->service_id) $set('favorite_target', 'service');
                            elseif ($record->provider_id) $set('favorite_target', 'provider');
                        }
                    })
                    ->required(),

                Select::make('product_id')
                    ->label($isAr ? 'المنتج' : 'Product')
                    ->relationship('product', 'name_ar')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name_ar ?? $record->name_en ?? 'Product #' . $record->id)
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get) => $get('favorite_target') === 'product')
                    ->required(fn ($get) => $get('favorite_target') === 'product'),

                Select::make('service_id')
                    ->label($isAr ? 'الخدمة' : 'Service')
                    ->relationship('service', 'service_ar')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->service_ar ?? $record->service_en ?? 'Service #' . $record->id)
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get) => $get('favorite_target') === 'service')
                    ->required(fn ($get) => $get('favorite_target') === 'service'),

                Select::make('provider_id')
                    ->label($isAr ? 'مقدم الخدمة' : 'Provider')
                    ->relationship('provider', 'title_ar')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->title_ar ?? $record->title_en ?? 'Provider #' . $record->id)
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get) => $get('favorite_target') === 'provider')
                    ->required(fn ($get) => $get('favorite_target') === 'provider'),

                Toggle::make('is_active')
                    ->label($isAr ? 'نشط' : 'Active')
                    ->default(true)
                    ->required(),
            ]);
    }
}
