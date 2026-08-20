<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('messages.banner_details'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title_ar')
                                    ->label(__('messages.title_ar'))
                                    ->nullable(),
                                TextInput::make('title_en')
                                    ->label(__('messages.title_en'))
                                    ->nullable(),
                                Textarea::make('desc_ar')
                                    ->label(__('messages.description_ar'))
                                    ->nullable()
                                    ->rows(3),
                                Textarea::make('desc_en')
                                    ->label(__('messages.description_en'))
                                    ->nullable()
                                    ->rows(3),
                                TextInput::make('link')
                                    ->label(__('messages.link'))
                                    ->nullable()
                                    ->placeholder('https://example.com or /offers'),
                                Select::make('type')
                                    ->label(__('messages.banner_type'))
                                    ->options([
                                        'home_page' => __('messages.home_page'),
                                        'product_page' => __('messages.product_page'),
                                        'coupon_page' => __('messages.coupon_page'),
                                    ])
                                    ->default('home_page')
                                    ->required(),
                                Select::make('status')
                                    ->label(__('messages.status'))
                                    ->options([
                                        'active' => __('messages.active'),
                                        'inactive' => __('messages.inactive'),
                                    ])
                                    ->default('active')
                                    ->required(),
                                FileUpload::make('image')
                                    ->label(__('messages.image'))
                                    ->image()
                                    ->directory('banners')
                                    ->nullable(),
                            ]),
                    ]),
            ]);
    }
}
