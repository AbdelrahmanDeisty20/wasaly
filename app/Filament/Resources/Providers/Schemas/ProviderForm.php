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
                Section::make()
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('provider_offering_hint')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString(
                                '<div style="display:flex; align-items:center; gap:8px; margin-bottom:2px;">' .
                                '<svg style="width:18px;height:18px;flex-shrink:0;" fill="none" stroke="#10b981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' .
                                '<p style="font-size:13px;font-weight:600;color:#10b981;margin:0;">تخصيص نوع تقديم الخدمة للمقدم</p>' .
                                '</div>' .
                                '<p style="font-size:11px;color:#a1a1aa;margin:0 0 0 26px;">يمكنك تحديد ما إذا كان مقدم الخدمة هذا يقدم خدمات فقط، أو يبيع منتجات فقط، أو يقدم كليهما معاً. بناءً على اختيارك، سيتم إظهار أو إخفاء أقسام الخدمات والمنتجات بالأسفل تسهيلاً للإدخال.</p>'
                            ))
                            ->columnSpanFull(),

                        Select::make('offering_type')
                            ->label(app()->getLocale() == 'ar' ? 'نوع الخدمات والمنتجات المقدمة' : 'Provider Offering Type')
                            ->options([
                                'both' => app()->getLocale() == 'ar' ? 'الخدمات والمنتجات معاً' : 'Both Services & Products',
                                'services' => app()->getLocale() == 'ar' ? 'الخدمات فقط' : 'Services Only',
                                'products' => app()->getLocale() == 'ar' ? 'المنتجات فقط' : 'Products Only',
                            ])
                            ->default('both')
                            ->live()
                            ->dehydrated(false)
                            ->afterStateHydrated(function ($state, $set, $record) {
                                if ($record) {
                                    $hasServices = $record->services()->exists();
                                    $hasProducts = $record->products()->exists();
                                    if ($hasServices && $hasProducts) {
                                        $set('offering_type', 'both');
                                    } elseif ($hasServices) {
                                        $set('offering_type', 'services');
                                    } elseif ($hasProducts) {
                                        $set('offering_type', 'products');
                                    }
                                }
                            })
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

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
                                    ->prefix('EGP'),
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
                    ->visible(fn ($get) => in_array($get('offering_type'), ['both', 'services']))
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
                                            ->prefix('EGP')
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

                Section::make(app()->getLocale() == 'ar' ? 'المنتجات' : 'Products')
                    ->visible(fn ($get) => in_array($get('offering_type'), ['both', 'products']))
                    ->schema([
                        Repeater::make('products')
                            ->relationship('products')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(2)
                                    ->schema([
                                        TextInput::make('name_ar')
                                            ->label(app()->getLocale() == 'ar' ? 'اسم المنتج بالعربية' : 'Product Name (AR)')
                                            ->required(),
                                        TextInput::make('name_en')
                                            ->label(app()->getLocale() == 'ar' ? 'اسم المنتج بالإنجليزية' : 'Product Name (EN)')
                                            ->required(),
                                        Textarea::make('description_ar')
                                            ->label(__('messages.description_ar'))
                                            ->rows(2)
                                            ->columnSpanFull(),
                                        Textarea::make('description_en')
                                            ->label(__('messages.description_en'))
                                            ->rows(2)
                                            ->columnSpanFull(),
                                        TextInput::make('price')
                                            ->label(__('messages.price'))
                                            ->numeric()
                                            ->prefix('EGP')
                                            ->required(),
                                        TextInput::make('stock')
                                            ->label(__('messages.stock'))
                                            ->numeric()
                                            ->default(0)
                                            ->required(),
                                        Select::make('brand_id')
                                            ->label(__('messages.brand'))
                                            ->relationship('brand', 'name_ar')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        Select::make('sub_category_id')
                                            ->label(__('messages.sub_category'))
                                            ->relationship(
                                                'subCategory',
                                                'name_ar',
                                                fn ($query) => $query->whereHas('category', fn ($q) => $q->where('name_ar', '!=', 'خدمات منزلية')->where('name_en', '!=', 'Home Services'))
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        Select::make('status')
                                            ->label(__('messages.status'))
                                            ->options([
                                                'active'   => __('messages.active'),
                                                'inactive' => __('messages.inactive'),
                                            ])
                                            ->default('active')
                                            ->required(),
                                        Toggle::make('is_featured')
                                            ->label(app()->getLocale() == 'ar' ? 'مميز؟' : 'Featured?')
                                            ->default(false),
                                        FileUpload::make('image')
                                            ->label(app()->getLocale() == 'ar' ? 'الصورة الرئيسية' : 'Main Image')
                                            ->image()
                                            ->disk('public')
                                            ->directory('products')
                                            ->columnSpan(2),

                                        Repeater::make('images')
                                            ->relationship('images')
                                            ->label(app()->getLocale() == 'ar' ? 'معرض صور المنتج' : 'Product Image Gallery')
                                            ->schema([
                                                FileUpload::make('images')
                                                    ->label(app()->getLocale() == 'ar' ? 'صورة إضافية' : 'Additional Image')
                                                    ->image()
                                                    ->disk('public')
                                                    ->directory('products/images')
                                                    ->required(),
                                            ])
                                            ->grid(3)
                                            ->collapsible()
                                            ->defaultItems(0)
                                            ->columnSpanFull()
                                            ->addActionLabel(app()->getLocale() == 'ar' ? 'إضافة صورة للمعرض' : 'Add Gallery Image'),

                                        Repeater::make('specifications')
                                            ->relationship('specifications')
                                            ->label(app()->getLocale() == 'ar' ? 'الخصائص والمواصفات' : 'Specifications')
                                            ->schema([
                                                \Filament\Schemas\Components\Grid::make(2)
                                                    ->schema([
                                                        TextInput::make('key_ar')
                                                            ->label(app()->getLocale() == 'ar' ? 'الخاصية بالعربية' : 'Key (AR)')
                                                            ->required(),
                                                        TextInput::make('key_en')
                                                            ->label(app()->getLocale() == 'ar' ? 'الخاصية بالإنجليزية' : 'Key (EN)')
                                                            ->required(),
                                                        TextInput::make('value_ar')
                                                            ->label(app()->getLocale() == 'ar' ? 'القيمة بالعربية' : 'Value (AR)')
                                                            ->required(),
                                                        TextInput::make('value_en')
                                                            ->label(app()->getLocale() == 'ar' ? 'القيمة بالإنجليزية' : 'Value (EN)')
                                                            ->required(),
                                                        FileUpload::make('icon')
                                                            ->label(app()->getLocale() == 'ar' ? 'أيقونة (اختياري)' : 'Icon (optional)')
                                                            ->image()
                                                            ->disk('public')
                                                            ->directory('specifications')
                                                            ->columnSpanFull(),
                                                    ]),
                                            ])
                                            ->collapsible()
                                            ->defaultItems(0)
                                            ->columnSpanFull()
                                            ->addActionLabel(app()->getLocale() == 'ar' ? 'إضافة خاصية / مواصفة' : 'Add Specification'),
                                    ]),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['name_ar'] ?? null)
                            ->collapsible()
                            ->cloneable()
                            ->defaultItems(0)
                            ->columnSpanFull()
                            ->addActionLabel(app()->getLocale() == 'ar' ? 'إضافة منتج جديد لهذا مقدم الخدمة' : 'Add New Product for this Provider'),
                    ]),

            ]);
    }
}
