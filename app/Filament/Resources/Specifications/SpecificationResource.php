<?php

namespace App\Filament\Resources\Specifications;

use App\Filament\Resources\Specifications\Pages\CreateSpecification;
use App\Filament\Resources\Specifications\Pages\EditSpecification;
use App\Filament\Resources\Specifications\Pages\ListSpecifications;
use App\Filament\Resources\Specifications\Pages\ViewSpecification;
use App\Filament\Resources\Specifications\Schemas\SpecificationForm;
use App\Filament\Resources\Specifications\Tables\SpecificationsTable;
use App\Models\Specification;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SpecificationResource extends Resource
{
    protected static ?string $model = Specification::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() == 'ar' ? 'خصائص المنتجات' : 'Product Specifications';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('messages.shop');
    }

    public static function getLabel(): ?string
    {
        return app()->getLocale() == 'ar' ? 'خاصية' : 'Specification';
    }

    public static function getPluralLabel(): ?string
    {
        return app()->getLocale() == 'ar' ? 'خصائص المنتجات' : 'Product Specifications';
    }

    protected static ?string $recordTitleAttribute = 'key_ar';

    public static function form(Schema $schema): Schema
    {
        return SpecificationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SpecificationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListSpecifications::route('/'),
            'create' => CreateSpecification::route('/create'),
            'view'   => ViewSpecification::route('/{record}'),
            'edit'   => EditSpecification::route('/{record}/edit'),
        ];
    }
}
