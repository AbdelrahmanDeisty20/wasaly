<?php

namespace App\Filament\Resources\Favorites\Pages;

use App\Filament\Resources\Favorites\FavoriteResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditFavorite extends EditRecord
{
    protected static string $resource = FavoriteResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
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

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
