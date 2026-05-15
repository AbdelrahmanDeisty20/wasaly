<?php

namespace App\Filament\Resources\Services;

use App\Filament\Resources\Services\Pages;
use App\Filament\Resources\Services\Schemas\ServiceForm;
use App\Filament\Resources\Services\Schemas\ServiceInfolist;
use App\Filament\Resources\Services\Tables\ServicesTable;
use App\Models\Service;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public static function getNavigationLabel(): string
    {
        return __('messages.services');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('messages.shop');
    }

    public static function getLabel(): ?string
    {
        return __('messages.service_ar');
    }

    public static function getPluralLabel(): ?string
    {
        return __('messages.services');
    }

    public static function form(Schema $schema): Schema
    {
        return ServiceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ServiceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServicesTable::configure($table);
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'view' => Pages\ViewService::route('/{record}'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
