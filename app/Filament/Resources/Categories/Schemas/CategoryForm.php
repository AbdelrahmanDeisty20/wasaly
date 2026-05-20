<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Wizard::make([
                    \Filament\Schemas\Components\Wizard\Step::make('نوع الإضافة')
                        ->description('ماذا تود أن تضيف اليوم؟')
                        ->visible(fn (string $operation): bool => $operation === 'create')
                        ->schema([
                            \Filament\Forms\Components\Radio::make('addition_type')
                                ->label('')
                                ->options([
                                    'main' => 'قسم رئيسي جديد (يمكنك إضافة أقسام فرعية له)',
                                    'sub_only' => 'أقسام فرعية فقط لقسم رئيسي موجود مسبقاً',
                                ])
                                ->default('main')
                                ->live()
                                ->required(),
                        ]),

                    \Filament\Schemas\Components\Wizard\Step::make(__('messages.category'))
                        ->description('بيانات القسم الرئيسي')
                        ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get, string $operation) => $operation === 'create' && $get('addition_type') === 'sub_only')
                        ->schema([
                            \Filament\Schemas\Components\Grid::make(2)
                                ->schema([
                                    TextInput::make('name_ar')
                                        ->label(__('messages.service_ar'))
                                        ->placeholder('مثال: مطاعم')
                                        ->required(fn (\Filament\Schemas\Components\Utilities\Get $get, string $operation) => $operation === 'edit' || $get('addition_type') === 'main'),
                                    TextInput::make('name_en')
                                        ->label(__('messages.service_en'))
                                        ->placeholder('e.g. Restaurants')
                                        ->required(fn (\Filament\Schemas\Components\Utilities\Get $get, string $operation) => $operation === 'edit' || $get('addition_type') === 'main'),
                                    Select::make('status')
                                        ->label(__('messages.status'))
                                        ->options([
                                            'active' => __('messages.active'),
                                            'inactive' => __('messages.inactive')
                                        ])
                                        ->default('active')
                                        ->required(),
                                    FileUpload::make('image')
                                        ->label(__('messages.image'))
                                        ->image()
                                        ->directory('categories'),
                                ]),
                        ]),

                    \Filament\Schemas\Components\Wizard\Step::make(__('messages.sub_categories'))
                        ->description('إضافة الأقسام الفرعية')
                        ->schema([
                            Select::make('parent_category_id')
                                ->label('اختر القسم الرئيسي')
                                ->options(\App\Models\Category::pluck('name_ar', 'id'))
                                ->searchable()
                                ->preload()
                                ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get, string $operation) => $operation === 'create' && $get('addition_type') === 'sub_only')
                                ->required(fn (\Filament\Schemas\Components\Utilities\Get $get, string $operation) => $operation === 'create' && $get('addition_type') === 'sub_only'),

                            \Filament\Forms\Components\Repeater::make('subCategories')
                                ->relationship('subCategories')
                                ->schema([
                                    \Filament\Schemas\Components\Grid::make(2)
                                        ->schema([
                                            TextInput::make('name_ar')
                                                ->label(__('messages.service_ar'))
                                                ->required(),
                                            TextInput::make('name_en')
                                                ->label(__('messages.service_en'))
                                                ->required(),
                                            Select::make('status')
                                                ->label(__('messages.status'))
                                                ->options([
                                                    'active' => __('messages.active'),
                                                    'inactive' => __('messages.inactive')
                                                ])
                                                ->default('active')
                                                ->required(),
                                            FileUpload::make('image')
                                                ->label(__('messages.image'))
                                                ->image()
                                                ->directory('subcategories'),
                                        ]),
                                ])
                                ->itemLabel(fn (array $state): ?string => $state['name_ar'] ?? null)
                                ->collapsible()
                                ->cloneable()
                                ->addActionLabel('إضافة قسم فرعي')
                                ->columns(1),
                        ]),
                ])
                ->columnSpanFull()
                ->skippable()
            ]);
    }
}
