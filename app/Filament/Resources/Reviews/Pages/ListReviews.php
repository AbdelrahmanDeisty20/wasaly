<?php

namespace App\Filament\Resources\Reviews\Pages;

use App\Filament\Resources\Reviews\ReviewResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListReviews extends ListRecords
{
    protected static string $resource = ReviewResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('messages.all_reviews')),
            
            'app' => Tab::make(__('messages.app_reviews'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('product_id')->whereNull('service_id')->whereNull('provider_id')),
                
            'products' => Tab::make(__('messages.product_reviews'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('product_id')),
                
            'services' => Tab::make(__('messages.service_reviews'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('service_id')),
                
            'providers' => Tab::make(__('messages.provider_reviews'))
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
