<?php

namespace App\Filament\Resources\Contacts\Pages;

use App\Filament\Resources\Contacts\ContactResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListContacts extends ListRecords
{
    protected static string $resource = ContactResource::class;

    public function getTabs(): array
    {
        $isAr = app()->getLocale() == 'ar';
        return [
            'all' => Tab::make($isAr ? 'كل الرسائل' : 'All Messages'),
            
            'general' => Tab::make($isAr ? 'الدعم العام' : 'General Support')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('service_id')->whereNull('provider_id')),
                
            'services' => Tab::make($isAr ? 'استفسارات الخدمات' : 'Service Inquiries')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('service_id')),
                
            'providers' => Tab::make($isAr ? 'تواصل مقدمي الخدمات' : 'Provider Inquiries')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('provider_id')),
        ];
    }
}
