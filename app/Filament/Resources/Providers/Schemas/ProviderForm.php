<?php

namespace App\Filament\Resources\Providers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ProviderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('messages.profile_info'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->label(__('messages.user'))
                                    ->relationship('user', 'name')
                                    ->getOptionLabelFromRecordUsing(fn (\App\Models\User $record) => $record->full_name ?? $record->phone ?? $record->email ?? ('مستخدم #' . $record->id))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        TextInput::make('full_name')
                                            ->label(app()->getLocale() == 'ar' ? 'الاسم الكامل' : 'Full Name')
                                            ->required(),
                                        TextInput::make('email')
                                            ->label(app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email')
                                            ->email()
                                            ->required()
                                            ->unique('users', 'email'),
                                        TextInput::make('phone')
                                            ->label(app()->getLocale() == 'ar' ? 'رقم الهاتف' : 'Phone Number')
                                            ->tel()
                                            ->required()
                                            ->unique('users', 'phone'),
                                        TextInput::make('password')
                                            ->label(app()->getLocale() == 'ar' ? 'كلمة المرور' : 'Password')
                                            ->password()
                                            ->required()
                                            ->default('password'),
                                        Select::make('type')
                                            ->label(app()->getLocale() == 'ar' ? 'النوع' : 'Type')
                                            ->options([
                                                'service_provider' => app()->getLocale() == 'ar' ? 'مقدم خدمة' : 'Service Provider',
                                                'user' => app()->getLocale() == 'ar' ? 'عميل' : 'Customer',
                                            ])
                                            ->default('service_provider')
                                            ->required(),
                                        Toggle::make('is_active')
                                            ->label(app()->getLocale() == 'ar' ? 'نشط' : 'Active')
                                            ->default(true),
                                    ]),
                                Select::make('sub_category_id')
                                    ->label(__('messages.sub_category'))
                                    ->relationship(
                                        'subCategory',
                                        'name_ar',
                                        fn ($query) => $query->whereHas('category', fn ($q) => $q->where('name_ar', 'خدمات منزلية')->orWhere('name_en', 'Home Services'))
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('title_ar')
                                    ->label(__('messages.service_ar'))
                                    ->required(),
                                TextInput::make('title_en')
                                    ->label(__('messages.service_en'))
                                    ->required(),
                            ]),
                    ]),

                Section::make(__('messages.description_ar'))
                    ->schema([
                        Textarea::make('service_description_ar')
                            ->label('')
                            ->required(),
                        Textarea::make('service_description_en')
                            ->label(__('messages.description_en'))
                            ->required(),
                    ]),

                Section::make(__('messages.pricing_inventory'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('price_from')
                                    ->label(__('messages.price'))
                                    ->numeric()
                                    ->prefix('SAR'),
                                Select::make('status')
                                    ->label(__('messages.status'))
                                    ->options([
                                        'active' => __('messages.active'),
                                        'inactive' => __('messages.inactive')
                                    ])
                                    ->default('active')
                                    ->required(),
                                FileUpload::make('cover')
                                    ->label(__('messages.image'))
                                    ->image()
                                    ->directory('providers'),
                            ]),
                    ]),
            ]);
    }
}
