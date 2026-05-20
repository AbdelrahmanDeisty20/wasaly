<?php

namespace App\Filament\Resources\Specifications\Pages;

use App\Filament\Resources\Specifications\SpecificationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSpecification extends ViewRecord
{
    protected static string $resource = SpecificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
