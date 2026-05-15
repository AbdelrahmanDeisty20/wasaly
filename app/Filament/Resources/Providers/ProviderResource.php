<?php

namespace App\Filament\Resources\Providers;

use App\Filament\Resources\Providers\Pages;
use App\Filament\Resources\Providers\Schemas\ProviderForm;
use App\Filament\Resources\Providers\Schemas\ProviderInfolist;
use App\Filament\Resources\Providers\Tables\ProvidersTable;
use App\Models\Provider;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use BackedEnum;

class ProviderResource extends Resource
{
    protected static ?string $model = Provider::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    public static function getNavigationLabel(): string
    {
        return __('messages.service_provider');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('messages.user_management');
    }

    public static function getLabel(): ?string
    {
        return __('messages.service_provider');
    }

    public static function getPluralLabel(): ?string
    {
        return __('messages.service_provider');
    }

    public static function form(Schema $schema): Schema
    {
        return ProviderForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProviderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProvidersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProviders::route('/'),
            'create' => Pages\CreateProvider::route('/create'),
            'view' => Pages\ViewProvider::route('/{record}'),
            'edit' => Pages\EditProvider::route('/{record}/edit'),
        ];
    }
}
