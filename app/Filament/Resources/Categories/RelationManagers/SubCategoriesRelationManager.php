<?php

namespace App\Filament\Resources\Categories\RelationManagers;

use App\Filament\Resources\SubCategories\Schemas\SubCategoryForm;
use App\Filament\Resources\SubCategories\Tables\SubCategoriesTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Schemas\Schema;

class SubCategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'subCategories';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('messages.sub_categories');
    }

    public function form(Schema $schema): Schema
    {
        return SubCategoryForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return SubCategoriesTable::configure($table)
            ->headerActions([
                \Filament\Tables\Actions\CreateAction::make(),
            ]);
    }
}
