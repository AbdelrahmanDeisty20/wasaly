<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('messages.booking_info'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->label(__('messages.user'))
                                    ->relationship('user', 'full_name')
                                    ->getOptionLabelFromRecordUsing(fn (\App\Models\User $record) => $record->full_name ?? $record->phone ?? $record->email ?? ('مستخدم #' . $record->id))
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('service_id')
                                    ->label(__('messages.service_ar'))
                                    ->relationship('service', 'service_ar')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('provider_id')
                                    ->label(__('messages.service_provider'))
                                    ->relationship('provider', 'title_ar')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('status')
                                    ->label(__('messages.status'))
                                    ->options([
                                        'pending' => __('messages.pending'),
                                        'accepted' => __('messages.accepted'),
                                        'processing' => __('messages.processing'),
                                        'shipped' => __('messages.shipped'),
                                        'delivered' => __('messages.delivered'),
                                        'cancelled' => __('messages.cancelled'),
                                    ])
                                    ->default('pending')
                                    ->required(),
                            ]),
                    ]),

                Section::make(app()->getLocale() == 'ar' ? 'تحديد الموعد (اليوم والوقت)' : 'Appointment Schedule (Day & Time)')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('available_day_id')
                                    ->label(app()->getLocale() == 'ar' ? 'اليوم المتاح' : 'Available Day')
                                    ->relationship('availableDay', 'name_ar')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => app()->getLocale() == 'ar' ? $record->name_ar : $record->name_en)
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('available_time_id')
                                    ->label(app()->getLocale() == 'ar' ? 'الوقت المتاح' : 'Available Time')
                                    ->relationship('availableTime', 'time')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),
                    ]),

                Section::make(__('messages.problem_description'))
                    ->schema([
                        Textarea::make('problem_description')
                            ->label('')
                            ->rows(3),
                    ]),
            ]);
    }
}
