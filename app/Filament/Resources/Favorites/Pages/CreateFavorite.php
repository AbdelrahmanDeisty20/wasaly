<?php

namespace App\Filament\Resources\Favorites\Pages;

use App\Filament\Resources\Favorites\FavoriteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFavorite extends CreateRecord
{
    protected static string $resource = FavoriteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['favorite_target'] ?? null) === 'product') {
            $data['service_id'] = null;
            $data['provider_id'] = null;
        } elseif (($data['favorite_target'] ?? null) === 'service') {
            $data['product_id'] = null;
            $data['provider_id'] = null;
        } elseif (($data['favorite_target'] ?? null) === 'provider') {
            $data['product_id'] = null;
            $data['service_id'] = null;
        }
        return $data;
    }
}
