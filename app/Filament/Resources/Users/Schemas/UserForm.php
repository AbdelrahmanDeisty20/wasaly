<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make(app()->getLocale() == 'ar' ? 'معلومات المستخدم' : 'User Information')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                \Filament\Forms\Components\FileUpload::make('avatar')
                                    ->label(app()->getLocale() == 'ar' ? 'الصورة الشخصية' : 'Avatar')
                                    ->image()
                                    ->directory('users/avatars')
                                    ->columnSpanFull(),
                                TextInput::make('name')
                                    ->label(app()->getLocale() == 'ar' ? 'الاسم المختصر' : 'Name')
                                    ->required(),
                                TextInput::make('full_name')
                                    ->label(app()->getLocale() == 'ar' ? 'الاسم الكامل' : 'Full Name'),
                                TextInput::make('email')
                                    ->label(app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email Address')
                                    ->email()
                                    ->required(),
                                TextInput::make('phone')
                                    ->label(app()->getLocale() == 'ar' ? 'رقم الهاتف' : 'Phone')
                                    ->tel(),
                                Select::make('type')
                                    ->label(app()->getLocale() == 'ar' ? 'نوع الحساب' : 'Account Type')
                                    ->options([
                                        'user' => app()->getLocale() == 'ar' ? 'عميل عادي' : 'User',
                                        'service_provider' => app()->getLocale() == 'ar' ? 'مقدم خدمة' : 'Service Provider'
                                    ])
                                    ->default('user')
                                    ->required()
                                    ->reactive(),
                                \Filament\Forms\Components\Select::make('linked_provider_id')
                                    ->label(app()->getLocale() == 'ar' ? 'مقدم الخدمة المرتبط (إن وجد)' : 'Linked Provider')
                                    ->options(fn () => \App\Models\Provider::pluck(app()->getLocale() == 'ar' ? 'title_ar' : 'title_en', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn ($get) => $get('type') === 'service_provider')
                                    ->afterStateHydrated(function ($state, $set, $record) {
                                        if ($record) {
                                            $provider = \App\Models\Provider::where('user_id', $record->id)->first();
                                            $set('linked_provider_id', $provider?->id);
                                        }
                                    })
                                    ->saveRelationshipsUsing(function ($state, $record) {
                                        // 1. Remove this user from any other provider
                                        \App\Models\Provider::where('user_id', $record->id)->update(['user_id' => null]);
                                        
                                        // 2. Associate this user with the selected provider
                                        if ($state) {
                                            \App\Models\Provider::where('id', $state)->update(['user_id' => $record->id]);
                                        }
                                    })
                                    ->dehydrated(false),
                                TextInput::make('provider')
                                    ->label(app()->getLocale() == 'ar' ? 'مزود التسجيل (جوجل، الخ)' : 'Auth Provider (Google, etc)'),
                                DateTimePicker::make('email_verified_at')
                                    ->label(app()->getLocale() == 'ar' ? 'تاريخ التوثيق' : 'Verified At'),
                                TextInput::make('password')
                                    ->label(app()->getLocale() == 'ar' ? 'كلمة المرور' : 'Password')
                                    ->password()
                                    ->dehydrateStateUsing(fn ($state) => filled($state) ? \Illuminate\Support\Facades\Hash::make($state) : null)
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn (string $operation): bool => $operation === 'create'),
                                Toggle::make('is_active')
                                    ->label(app()->getLocale() == 'ar' ? 'حساب نشط' : 'Is Active')
                                    ->default(true)
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
