<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make()
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('provider_hint_text')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString(
                                '<div style="display:flex; align-items:center; gap:8px; margin-bottom:2px;">' .
                                '<svg style="width:18px;height:18px;flex-shrink:0;" fill="none" stroke="#6366f1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' .
                                '<p style="font-size:13px;font-weight:600;color:#6366f1;margin:0;">' . __('messages.is_provider_product') . '</p>' .
                                '</div>' .
                                '<p style="font-size:11px;color:#a1a1aa;margin:0 0 0 26px;">' . __('messages.provider_hint') . '</p>'
                            ))
                            ->columnSpanFull(),

                        Select::make('provider_id')
                            ->label(__('messages.optional_provider'))
                            ->relationship('provider', 'title_ar')
                            ->getOptionLabelFromRecordUsing(fn (\App\Models\Provider $record) => ($record->title_ar ?? $record->title_en ?? ('مقدم #' . $record->id)) . ' — ' . ($record->user?->full_name ?? ''))
                            ->searchable()
                            ->preload()
                            ->placeholder(__('messages.without_provider'))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                \Filament\Schemas\Components\Tabs::make(__('messages.product_details'))
                    ->tabs([
                        \Filament\Schemas\Components\Tabs\Tab::make(__('messages.product_details'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        \Filament\Schemas\Components\Group::make([
                                            \Filament\Schemas\Components\Section::make()
                                                ->schema([
                                                    TextInput::make('name_ar')
                                                        ->label(__('messages.service_ar'))
                                                        ->required(),
                                                    TextInput::make('name_en')
                                                        ->label(__('messages.service_en'))
                                                        ->required(),
                                                    Textarea::make('description_ar')
                                                        ->label(__('messages.description_ar'))
                                                        ->required()
                                                        ->rows(5),
                                                    Textarea::make('description_en')
                                                        ->label(__('messages.description_en'))
                                                        ->required()
                                                        ->rows(5),
                                                ]),
                                        ])->columnSpan(2),

                                        \Filament\Schemas\Components\Group::make([
                                            \Filament\Schemas\Components\Section::make(__('messages.pricing_inventory'))
                                                ->schema([
                                                    TextInput::make('price')
                                                        ->label(__('messages.price'))
                                                        ->numeric()
                                                        ->prefix('EGP')
                                                        ->required(),
                                                    TextInput::make('stock')
                                                        ->label(__('messages.stock'))
                                                        ->numeric()
                                                        ->default(1)
                                                        ->required(),
                                                    Toggle::make('is_featured')
                                                        ->label(__('messages.is_featured'))
                                                        ->onColor('success'),
                                                    Select::make('status')
                                                        ->label(__('messages.status'))
                                                        ->options([
                                                            'active' => __('messages.active'),
                                                            'inactive' => __('messages.inactive')
                                                        ])
                                                        ->default('active')
                                                        ->required(),
                                                ]),

                                            \Filament\Schemas\Components\Section::make(__('messages.associations'))
                                                ->schema([
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
                                                    Select::make('brand_id')
                                                        ->label(__('messages.brand'))
                                                        ->relationship('brand', 'name_ar')
                                                        ->searchable()
                                                        ->preload(),
                                                ]),

                                            \Filament\Schemas\Components\Section::make(__('messages.image'))
                                                ->schema([
                                                    FileUpload::make('image')
                                                        ->label('')
                                                        ->image()
                                                        ->directory('products')
                                                        ->required(),
                                                ]),
                                        ])->columnSpan(1),
                                    ]),
                            ]),



                        \Filament\Schemas\Components\Tabs\Tab::make(__('messages.gallery'))
                            ->icon('heroicon-o-photo')
                            ->schema([
                                \Filament\Forms\Components\Repeater::make('images')
                                    ->relationship()
                                    ->schema([
                                        FileUpload::make('images')
                                            ->label('')
                                            ->image()
                                            ->directory('products/images')
                                            ->required(),
                                    ])
                                    ->grid(4)
                                    ->addActionLabel(__('messages.add_image'))
                                    ->collapsible(),
                            ]),

                        \Filament\Schemas\Components\Tabs\Tab::make(app()->getLocale() == 'ar' ? 'الخصائص (المواصفات)' : 'Specifications')
                            ->icon('heroicon-o-adjustments-horizontal')
                            ->schema([
                                \Filament\Forms\Components\Placeholder::make('specifications_hint')
                                    ->label('')
                                    ->content(new \Illuminate\Support\HtmlString(
                                        '<div style="display:flex; align-items:center; gap:8px; margin-bottom:2px;">' .
                                        '<svg style="width:18px;height:18px;flex-shrink:0;" fill="none" stroke="#6366f1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' .
                                        '<p style="font-size:13px;font-weight:600;color:#6366f1;margin:0;">إضافة خصائص للمنتج (اختياري)</p>' .
                                        '</div>' .
                                        '<p style="font-size:11px;color:#a1a1aa;margin:0 0 0 26px;">يمكنك إضافة هذا المنتج بمفرده بدون خصائص، أو يمكنك إضافة خصائص ومواصفات له مثل المقاس أو اللون أو غيرها مباشرة من هنا.</p>'
                                    ))
                                    ->columnSpanFull(),

                                \Filament\Forms\Components\Repeater::make('specifications')
                                    ->relationship('specifications')
                                    ->schema([
                                        TextInput::make('key_ar')
                                            ->label(__('messages.key_ar') ?? 'الاسم (عربي)')
                                            ->required(),
                                        TextInput::make('key_en')
                                            ->label(__('messages.key_en') ?? 'الاسم (إنجليزي)')
                                            ->required(),
                                        TextInput::make('value_ar')
                                            ->label(__('messages.value_ar') ?? 'القيمة (عربي)')
                                            ->required(),
                                        TextInput::make('value_en')
                                            ->label(__('messages.value_en') ?? 'القيمة (إنجليزي)')
                                            ->required(),
                                        FileUpload::make('icon')
                                            ->label(__('messages.icon') ?? 'الأيقونة')
                                            ->image()
                                            ->directory('specifications')
                                            ->columnSpanFull()
                                            ->required(),
                                    ])
                                    ->grid(2)
                                    ->collapsible()
                                    ->defaultItems(0)
                                    ->addActionLabel(app()->getLocale() == 'ar' ? 'إضافة خاصية / مواصفة جديدة' : 'Add Specification'),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }
}
