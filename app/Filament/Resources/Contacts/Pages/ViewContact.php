<?php

namespace App\Filament\Resources\Contacts\Pages;

use App\Filament\Resources\Contacts\ContactResource;
use Filament\Resources\Pages\ViewRecord;

class ViewContact extends ViewRecord
{
    protected static string $resource = ContactResource::class;

    protected function fillForm(): void
    {
        parent::fillForm();
        
        // Auto mark message as read when opened by admin
        if ($this->record && !$this->record->is_read) {
            $this->record->update(['is_read' => true]);
        }
    }
}
