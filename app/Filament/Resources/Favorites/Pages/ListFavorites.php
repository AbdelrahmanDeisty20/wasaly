<?php

namespace App\Filament\Resources\Favorites\Pages;

use App\Filament\Resources\Favorites\FavoriteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFavorites extends ListRecords
{
    protected static string $resource = FavoriteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $isAr = app()->getLocale() == 'ar';
        return [
            'all' => \Filament\Schemas\Components\Tabs\Tab::make($isAr ? 'الكل' : 'All'),
            'products' => \Filament\Schemas\Components\Tabs\Tab::make($isAr ? 'مفضلات المنتجات' : 'Product Favorites')
                ->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->whereNotNull('product_id')),
            'services' => \Filament\Schemas\Components\Tabs\Tab::make($isAr ? 'مفضلات الخدمات' : 'Service Favorites')
                ->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->whereNotNull('service_id')),
            'providers' => \Filament\Schemas\Components\Tabs\Tab::make($isAr ? 'مفضلات مقدمي الخدمات' : 'Provider Favorites')
                ->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->whereNotNull('provider_id')),
        ];
    }
}
