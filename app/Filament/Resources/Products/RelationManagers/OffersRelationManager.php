<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class OffersRelationManager extends RelationManager
{
    protected static string $relationship = 'offers';

    protected static ?string $title = 'العروض';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('discount_percentage')
                    ->label('نسبة الخصم (%)')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(100),
                Forms\Components\DateTimePicker::make('start_date')
                    ->label('تاريخ البداية')
                    ->required(),
                Forms\Components\DateTimePicker::make('end_date')
                    ->label('تاريخ النهاية')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label('نشط')
                    ->default(true)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('discount_percentage')
                    ->label('نسبة الخصم (%)')
                    ->badge()
                    ->color('success')
                    ->suffix('%'),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('تاريخ البداية')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('تاريخ النهاية')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('نشط'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('إضافة عرض'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
