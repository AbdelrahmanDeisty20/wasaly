<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        $isAr = app()->getLocale() == 'ar';
        return [
            Action::make('changeStatus')
                ->label($isAr ? 'تغيير حالة الطلب' : 'Change Status')
                ->icon('heroicon-m-arrow-path')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\Select::make('status')
                        ->label($isAr ? 'الحالة الجديدة' : 'New Status')
                        ->options([
                            'pending' => __('messages.pending'),
                            'accepted' => __('messages.accepted'),
                            'processing' => __('messages.processing'),
                            'shipped' => __('messages.shipped'),
                            'delivered' => __('messages.delivered'),
                            'cancelled' => __('messages.cancelled'),
                        ])
                        ->required()
                        ->default(fn ($record) => $record->status),
                ])
                ->action(function ($record, array $data) use ($isAr) {
                    $record->update(['status' => $data['status']]);
                    
                    Notification::make()
                        ->title($isAr ? 'تم تحديث حالة الطلب بنجاح' : 'Order status updated successfully')
                        ->success()
                        ->send();
                }),
            EditAction::make(),
        ];
    }
}
