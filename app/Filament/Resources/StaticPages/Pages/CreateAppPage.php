<?php

namespace App\Filament\Resources\StaticPages\Pages;

use App\Filament\Resources\StaticPages\AppPageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAppPage extends CreateRecord
{
    protected static string $resource = AppPageResource::class;
}
