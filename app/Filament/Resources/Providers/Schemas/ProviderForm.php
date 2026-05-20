<?php

namespace App\Filament\Resources\Providers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TimePicker;
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
                                    ->disk('public')
                                    ->directory('providers'),
                            ]),
                    ]),

                Section::make(app()->getLocale() == 'ar' ? 'أوقات العمل والجدول' : 'Working Hours & Schedule')
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(4)
                            ->schema([
                                Select::make('from_day')
                                    ->label(app()->getLocale() == 'ar' ? 'من يوم' : 'From Day')
                                    ->options([
                                        'Saturday' => app()->getLocale() == 'ar' ? 'السبت' : 'Saturday',
                                        'Sunday' => app()->getLocale() == 'ar' ? 'الأحد' : 'Sunday',
                                        'Monday' => app()->getLocale() == 'ar' ? 'الاثنين' : 'Monday',
                                        'Tuesday' => app()->getLocale() == 'ar' ? 'الثلاثاء' : 'Tuesday',
                                        'Wednesday' => app()->getLocale() == 'ar' ? 'الأربعاء' : 'Wednesday',
                                        'Thursday' => app()->getLocale() == 'ar' ? 'الخميس' : 'Thursday',
                                        'Friday' => app()->getLocale() == 'ar' ? 'الجمعة' : 'Friday',
                                    ])
                                    ->default('Saturday')
                                    ->required(),
                                Select::make('to_day')
                                    ->label(app()->getLocale() == 'ar' ? 'إلى يوم' : 'To Day')
                                    ->options([
                                        'Saturday' => app()->getLocale() == 'ar' ? 'السبت' : 'Saturday',
                                        'Sunday' => app()->getLocale() == 'ar' ? 'الأحد' : 'Sunday',
                                        'Monday' => app()->getLocale() == 'ar' ? 'الاثنين' : 'Monday',
                                        'Tuesday' => app()->getLocale() == 'ar' ? 'الثلاثاء' : 'Tuesday',
                                        'Wednesday' => app()->getLocale() == 'ar' ? 'الأربعاء' : 'Wednesday',
                                        'Thursday' => app()->getLocale() == 'ar' ? 'الخميس' : 'Thursday',
                                        'Friday' => app()->getLocale() == 'ar' ? 'الجمعة' : 'Friday',
                                    ])
                                    ->default('Thursday')
                                    ->required(),
                                TimePicker::make('start_time')
                                    ->label(app()->getLocale() == 'ar' ? 'وقت البدء' : 'Start Time')
                                    ->default('09:00:00')
                                    ->required(),
                                TimePicker::make('end_time')
                                    ->label(app()->getLocale() == 'ar' ? 'وقت الانتهاء' : 'End Time')
                                    ->default('21:00:00')
                                    ->required(),
                            ]),
                    ]),

                Section::make(__('messages.services') ?? 'الخدمات')
                    ->schema([
                        Repeater::make('services')
                            ->relationship('services')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(2)
                                    ->schema([
                                        TextInput::make('service_ar')
                                            ->label(__('messages.service_ar'))
                                            ->required(),
                                        TextInput::make('service_en')
                                            ->label(__('messages.service_en'))
                                            ->required(),
                                        Textarea::make('description_ar')
                                            ->label(__('messages.description_ar'))
                                            ->required()
                                            ->rows(2)
                                            ->columnSpanFull(),
                                        Textarea::make('description_en')
                                            ->label(__('messages.description_en'))
                                            ->required()
                                            ->rows(2)
                                            ->columnSpanFull(),
                                        TextInput::make('price')
                                            ->label(__('messages.price'))
                                            ->numeric()
                                            ->prefix('SAR')
                                            ->required(),
                                        FileUpload::make('image')
                                            ->label(app()->getLocale() == 'ar' ? 'الصورة الرئيسية للخدمة' : 'Main Service Image')
                                            ->image()
                                            ->disk('public')
                                            ->directory('services')
                                            ->required(),
                                        Repeater::make('serviceImages')
                                            ->relationship('serviceImages')
                                            ->schema([
                                                FileUpload::make('images')
                                                    ->label(app()->getLocale() == 'ar' ? 'صورة إضافية' : 'Additional Image')
                                                    ->image()
                                                    ->disk('public')
                                                    ->directory('services')
                                                    ->required(),
                                            ])
                                            ->grid(3)
                                            ->columnSpanFull()
                                            ->addActionLabel(app()->getLocale() == 'ar' ? 'إضافة صورة لمعرض الخدمة' : 'Add Image to Service Gallery'),
                                    ]),
                            ])
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $get): array {
                                $data['sub_category_id'] = $get('sub_category_id');
                                return $data;
                            })
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data, $get): array {
                                $data['sub_category_id'] = $get('sub_category_id');
                                return $data;
                            })
                            ->itemLabel(fn (array $state): ?string => $state['service_ar'] ?? null)
                            ->collapsible()
                            ->cloneable()
                            ->defaultItems(0)
                            ->addActionLabel(app()->getLocale() == 'ar' ? 'إضافة خدمة جديدة لهذا مقدم الخدمة' : 'Add New Service for this Provider'),
                    ]),
            ]);
    }
}
