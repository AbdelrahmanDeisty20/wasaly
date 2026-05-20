<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                \Filament\Schemas\Components\Section::make(app()->getLocale() == 'ar' ? 'المعلومات الشخصية' : 'Personal Information')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(12)
                            ->schema([
                                \Filament\Schemas\Components\Group::make([
                                    \Filament\Infolists\Components\ImageEntry::make('avatar')
                                        ->label(app()->getLocale() == 'ar' ? 'الصورة الشخصية' : 'Avatar')
                                        ->hiddenLabel()
                                        ->circular()
                                        ->size(120)
                                        ->defaultImageUrl('https://ui-avatars.com/api/?name=User&color=FFFFFF&background=111827'),
                                ])->columnSpan(12),
                                \Filament\Schemas\Components\Group::make([
                                    \Filament\Schemas\Components\Grid::make(2)
                                        ->schema([
                                            TextEntry::make('name')
                                                ->label(app()->getLocale() == 'ar' ? 'الاسم المختصر' : 'Name')
                                                ->weight('bold')
                                                ->size('lg'),
                                            TextEntry::make('full_name')
                                                ->label(app()->getLocale() == 'ar' ? 'الاسم الكامل' : 'Full Name')
                                                ->placeholder('-'),
                                            TextEntry::make('email')
                                                ->label(app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email Address')
                                                ->icon('heroicon-m-envelope')
                                                ->copyable(),
                                            TextEntry::make('phone')
                                                ->label(app()->getLocale() == 'ar' ? 'رقم الهاتف' : 'Phone')
                                                ->icon('heroicon-m-phone')
                                                ->placeholder('-'),
                                        ]),
                                ])->columnSpan(7),
                                \Filament\Schemas\Components\Group::make([
                                    \Filament\Schemas\Components\Section::make(app()->getLocale() == 'ar' ? 'حالة الحساب' : 'Account Status')
                                        ->schema([
                                            TextEntry::make('type')
                                                ->label(app()->getLocale() == 'ar' ? 'نوع الحساب' : 'Account Type')
                                                ->badge()
                                                ->color(fn (string $state): string => match ($state) {
                                                    'user' => 'success',
                                                    'service_provider' => 'info',
                                                    default => 'gray',
                                                })
                                                ->formatStateUsing(fn (string $state): string => __("messages.{$state}")),
                                            IconEntry::make('is_active')
                                                ->label(app()->getLocale() == 'ar' ? 'نشط' : 'Active')
                                                ->boolean(),
                                            TextEntry::make('provider')
                                                ->label(app()->getLocale() == 'ar' ? 'جهة التسجيل' : 'Provider')
                                                ->placeholder('-'),
                                            TextEntry::make('email_verified_at')
                                                ->label(app()->getLocale() == 'ar' ? 'تاريخ التوثيق' : 'Verified At')
                                                ->dateTime()
                                                ->placeholder(app()->getLocale() == 'ar' ? 'غير موثق' : 'Unverified')
                                                ->badge()
                                                ->color('success'),
                                        ]),
                                ])->columnSpan(5),
                            ]),
                    ]),
            ]);
    }
}
