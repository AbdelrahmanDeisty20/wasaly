<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewBooking extends ViewRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        $isAr = app()->getLocale() == 'ar';
        return [
            Action::make('changeStatus')
                ->label($isAr ? 'تغيير حالة الحجز' : 'Change Booking Status')
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
                        ->title($isAr ? 'تم تحديث حالة الحجز بنجاح' : 'Booking status updated successfully')
                        ->success()
                        ->send();
                }),
            EditAction::make(),
        ];
    }
}
