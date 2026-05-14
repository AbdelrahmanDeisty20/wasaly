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
                    \Filament\Schemas\Components\Wizard\Step::make(__('messages.category'))
                        ->description('البيانات الأساسية للقسم الرئيسي')
                        ->schema([
                            \Filament\Schemas\Components\Grid::make(2)
                                ->schema([
                                    TextInput::make('name_ar')
                                        ->label(__('messages.service_ar'))
                                        ->placeholder('مثال: مطاعم')
                                        ->required(),
                                    TextInput::make('name_en')
                                        ->label(__('messages.service_en'))
                                        ->placeholder('e.g. Restaurants')
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
                                        ->directory('categories')
                                        ->required(),
                                ]),
                        ]),
                    \Filament\Schemas\Components\Wizard\Step::make(__('messages.sub_categories'))
                        ->description('إضافة الأقسام الفرعية التابعة (اختياري)')
                        ->schema([
                            \Filament\Forms\Components\Repeater::make('subCategories')
                                ->relationship()
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
                                                ->directory('subcategories')
                                                ->required(),
                                        ]),
                                ])
                                ->itemLabel(fn (array $state): ?string => $state['name_ar'] ?? null)
                                ->collapsible()
                                ->cloneable()
                                ->addActionLabel('إضافة قسم فرعي جديد')
                                ->columns(1),
                        ]),
                ])
                ->columnSpanFull()
                ->skippable() // Allow skipping to subcategories or finishing early
            ]);
    }
}
