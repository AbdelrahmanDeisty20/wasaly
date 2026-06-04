<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BrandInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make()
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(12)
                            ->schema([
                                ImageEntry::make('image')
                                    ->label('')
                                    ->state(fn ($record) => $record->image_path)
                                    ->columnSpan(3)
                                    ->circular()
                                    ->size(100)
                                    ->extraImgAttributes(['class' => 'shadow-md']),
                                
                                \Filament\Schemas\Components\Group::make([
                                    TextEntry::make('name_ar')
                                        ->label('')
                                        ->weight('bold')
                                        ->size('lg')
                                        ->color('primary'),
                                    TextEntry::make('name_en')
                                        ->label('')
                                        ->size('md')
                                        ->color('gray'),
                                    
                                    TextEntry::make('status')
                                        ->label(__('messages.status'))
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            'active' => 'success',
                                            'inactive' => 'danger',
                                            default => 'gray',
                                        }),
                                    
                                    TextEntry::make('created_at')
                                        ->label('تاريخ الإضافة')
                                        ->dateTime()
                                        ->color('gray'),
                                ])->columnSpan(9),
                            ]),
                    ]),
            ]);
    }
}
