<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('messages.all_products')),
            'admin' => Tab::make(__('messages.admin_products'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('provider_id')),
            'providers' => Tab::make(__('messages.providers_products'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('provider_id')),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
