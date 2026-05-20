<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
                CreateAction::make()->label('إضافة عرض'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
