<?php

namespace App\Filament\Resources\Favorites\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FavoriteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('user_id')
                    ->label(app()->getLocale() == 'ar' ? 'المستخدم' : 'User')
                    ->relationship('user', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name ?? $record->email ?? 'User #' . $record->id)
                    ->searchable()
                    ->preload()
                    ->required(),
                \Filament\Forms\Components\Select::make('product_id')
                    ->label(app()->getLocale() == 'ar' ? 'المنتج' : 'Product')
                    ->relationship('product', 'name_ar')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name_ar ?? $record->name_en ?? 'Product #' . $record->id)
                    ->searchable()
                    ->preload(),
                \Filament\Forms\Components\Select::make('service_id')
                    ->label(app()->getLocale() == 'ar' ? 'الخدمة' : 'Service')
                    ->relationship('service', 'service_ar')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->service_ar ?? $record->service_en ?? 'Service #' . $record->id)
                    ->searchable()
                    ->preload(),
                \Filament\Forms\Components\Select::make('provider_id')
                    ->label(app()->getLocale() == 'ar' ? 'مقدم الخدمة' : 'Provider')
                    ->relationship('provider', 'title_ar')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->title_ar ?? $record->title_en ?? 'Provider #' . $record->id)
                    ->searchable()
                    ->preload(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
