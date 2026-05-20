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
                                    ->relationship('user', 'name', fn ($query) => $query->where('type', 'service_provider'))
                                    ->getOptionLabelFromRecordUsing(fn (\App\Models\User $record) => $record->full_name ?? $record->phone ?? $record->email ?? ('مستخدم #' . $record->id))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText(
                                        app()->getLocale() == 'ar'
                                            ? 'إذا لم تقم بإضافة مستخدم بشكل مستقل، يمكنك إضافته من علامة أو زر + !'
                                            : 'If you have not added a user independently, you can add them using the + button! Go ahead.'
                                    )
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
                                \Filament\Forms\Components\Placeholder::make('add_services_link')
                                    ->label('')
                                    ->content(new \Illuminate\Support\HtmlString(
                                        app()->getLocale() == 'ar'
                                            ? '<div style="padding: 16px; background-color: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 12px; display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-top: 8px; width: 100%;">' .
                                              '<div style="text-align: right; flex: 1;">' .
                                              '<h4 style="font-size: 14px; font-weight: 600; color: #3b82f6; margin: 0;">هل تريد إضافة خدمات مقدم الخدمة بالمرة؟</h4>' .
                                              '<p style="font-size: 12px; color: #a1a1aa; margin: 4px 0 0 0;">بعد الانتهاء من إضافة مقدم الخدمة، يمكنك الانتقال مباشرة لإضافة خدماته المتنوعة.</p>' .
                                              '</div>' .
                                              '<a href="' . route('filament.admin.resources.services.create') . '" target="_blank" style="flex-shrink: 0; display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; font-size: 12px; font-weight: bold; color: #ffffff; background-color: #3b82f6; border-radius: 8px; text-decoration: none; transition: background-color 0.15s ease-in-out;">' .
                                              '<svg style="width: 14px; height: 14px; fill: none; stroke: currentColor; stroke-width: 2; display: inline-block; vertical-align: middle;" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>' .
                                              'إضافة الخدمات الآن' .
                                              '</a>' .
                                              '</div>'
                                            : '<div style="padding: 16px; background-color: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 12px; display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-top: 8px; width: 100%;">' .
                                              '<div style="text-align: left; flex: 1;">' .
                                              '<h4 style="font-size: 14px; font-weight: 600; color: #3b82f6; margin: 0;">Do you want to add their services too?</h4>' .
                                              '<p style="font-size: 12px; color: #a1a1aa; margin: 4px 0 0 0;">After completing the provider profile, you can proceed directly to adding their various services.</p>' .
                                              '</div>' .
                                              '<a href="' . route('filament.admin.resources.services.create') . '" target="_blank" style="flex-shrink: 0; display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; font-size: 12px; font-weight: bold; color: #ffffff; background-color: #3b82f6; border-radius: 8px; text-decoration: none; transition: background-color 0.15s ease-in-out;">' .
                                              '<svg style="width: 14px; height: 14px; fill: none; stroke: currentColor; stroke-width: 2; display: inline-block; vertical-align: middle;" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>' .
                                              'Add Services Now' .
                                              '</a>' .
                                              '</div>'
                                    ))
                                    ->columnSpan(2),
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
