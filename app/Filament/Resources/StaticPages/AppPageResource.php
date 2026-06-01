<?php

namespace App\Filament\Resources\StaticPages;

use App\Filament\Resources\StaticPages\Pages\CreateAppPage;
use App\Filament\Resources\StaticPages\Pages\EditAppPage;
use App\Filament\Resources\StaticPages\Pages\ListAppPages;
use App\Filament\Resources\StaticPages\Pages\ViewAppPage;
use App\Filament\Resources\StaticPages\Schemas\AppPageForm;
use App\Filament\Resources\StaticPages\Schemas\AppPageInfolist;
use App\Filament\Resources\StaticPages\Tables\AppPagesTable;
use App\Models\Page;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AppPageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationLabel(): string
    {
        return __('messages.static_pages');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('messages.system');
    }

    public static function getLabel(): ?string
    {
        return __('messages.static_page');
    }

    public static function getPluralLabel(): ?string
    {
        return __('messages.static_pages');
    }

    protected static ?string $recordTitleAttribute = 'title_ar';

    public static function form(Schema $schema): Schema
    {
        return AppPageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AppPageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AppPagesTable::configure($table);
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
            'index' => ListAppPages::route('/'),
            'create' => CreateAppPage::route('/create'),
            'view' => ViewAppPage::route('/{record}'),
            'edit' => EditAppPage::route('/{record}/edit'),
        ];
    }
}
