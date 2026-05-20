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

class SpecificationsRelationManager extends RelationManager
{
    protected static string $relationship = 'specifications';

    protected static ?string $title = 'الخصائص (المواصفات)';

    protected static ?string $recordTitleAttribute = 'key_ar';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('key_ar')
                            ->label(__('messages.key_ar'))
                            ->required(),
                        Forms\Components\TextInput::make('key_en')
                            ->label(__('messages.key_en'))
                            ->required(),
                        Forms\Components\TextInput::make('value_ar')
                            ->label(__('messages.value_ar'))
                            ->required(),
                        Forms\Components\TextInput::make('value_en')
                            ->label(__('messages.value_en'))
                            ->required(),
                        Forms\Components\FileUpload::make('icon')
                            ->label(__('messages.icon'))
                            ->image()
                            ->directory('specifications')
                            ->columnSpanFull()
                            ->required(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('key_ar')
            ->columns([
                Tables\Columns\ImageColumn::make('icon')
                    ->label(__('messages.icon'))
                    ->disk('public')
                    ->state(function ($record) {
                        if (!$record->icon) return null;
                        return str_starts_with($record->icon, 'specifications/') ? $record->icon : 'specifications/' . $record->icon;
                    })
                    ->circular(),
                Tables\Columns\TextColumn::make('key_ar')
                    ->label(__('messages.key_ar')),
                Tables\Columns\TextColumn::make('value_ar')
                    ->label(__('messages.value_ar')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->label('إضافة خاصية'),
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
