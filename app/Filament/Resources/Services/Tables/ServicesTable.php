<?php

namespace App\Filament\Resources\Services\Tables;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label(__('messages.image'))
                    ->disk('public')
                    ->state(function ($record) {
                        if (!$record->image) return null;
                        return str_starts_with($record->image, 'services/') ? $record->image : 'services/' . $record->image;
                    })
                    ->circular(),
                TextColumn::make('service_ar')
                    ->label(__('messages.service_ar'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('provider.title_ar')
                    ->label(__('messages.service_provider'))
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('subCategory.name_ar')
                    ->label(__('messages.sub_category'))
                    ->badge()
                    ->color('info'),
                TextColumn::make('price')
                    ->label(__('messages.price'))
                    ->money('EGP')
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),
                TextColumn::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('provider_id')
                    ->label(app()->getLocale() == 'ar' ? 'مقدم الخدمة' : 'Provider')
                    ->relationship('provider', 'title_ar')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->title_ar ?? $record->title_en ?? 'Provider #' . $record->id)
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                \App\Helpers\FilamentExportHelper::makeImportHeaderAction(
                    'services',
                    function (array $row) {
                        // Find provider
                        $providerName = $row['provider_ar'] ?? $row['provider'] ?? $row['مقدم_الخدمة'] ?? '';
                        $provider = null;
                        if ($providerName) {
                            $provider = \App\Models\Provider::where('title_ar', $providerName)->orWhere('title_en', $providerName)->first();
                        }
                        $providerId = $provider ? $provider->id : (\App\Models\Provider::first()->id ?? 1);

                        // Find or create subcategory
                        $subCategoryName = $row['subcategory_ar'] ?? $row['subcategory'] ?? $row['القسم_الفرعي'] ?? '';
                        $subCategoryId = null;
                        if ($subCategoryName) {
                            $subCat = \App\Models\SubCategory::where('name_ar', $subCategoryName)->orWhere('name_en', $subCategoryName)->first();
                            if (!$subCat) {
                                $subCat = \App\Models\SubCategory::create([
                                    'name_ar' => $subCategoryName,
                                    'name_en' => $row['subcategory_en'] ?? $subCategoryName,
                                    'category_id' => \App\Models\Category::first()->id ?? 1,
                                    'status' => 'active',
                                    'image' => 'default.png',
                                ]);
                            }
                            $subCategoryId = $subCat->id;
                        }

                        \App\Models\Service::create([
                            'provider_id' => $providerId,
                            'sub_category_id' => $subCategoryId ?? 1,
                            'service_ar' => $row['service_ar'] ?? $row['الاسم_عربي'] ?? '',
                            'service_en' => $row['service_en'] ?? $row['الاسم_إنجليزي'] ?? '',
                            'description_ar' => $row['description_ar'] ?? $row['الوصف_عربي'] ?? '',
                            'description_en' => $row['description_en'] ?? $row['الوصف_إنجليزي'] ?? '',
                            'price' => floatval($row['price'] ?? $row['السعر'] ?? 0),
                            'image' => 'default.png',
                        ]);
                    }
                ),
                \App\Helpers\FilamentExportHelper::makeExportHeaderAction(
                    'services',
                    [
                        'ID',
                        'الخدمة (عربي)',
                        'الخدمة (إنجليزي)',
                        'الوصف (عربي)',
                        'الوصف (إنجليزي)',
                        'مقدم الخدمة',
                        'القسم الفرعي',
                        'السعر',
                        'تاريخ الإنشاء',
                    ],
                    fn ($record) => [
                        $record->id,
                        $record->service_ar,
                        $record->service_en,
                        $record->description_ar,
                        $record->description_en,
                        $record->provider?->title_ar ?? '',
                        $record->subCategory?->name_ar ?? '',
                        $record->price,
                        $record->created_at?->toDateTimeString() ?? '',
                    ],
                    \App\Models\Service::class
                )
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    \App\Helpers\FilamentExportHelper::makeExportBulkAction(
                        'services',
                        [
                            'ID',
                            'الخدمة (عربي)',
                            'الخدمة (إنجليزي)',
                            'الوصف (عربي)',
                            'الوصف (إنجليزي)',
                            'مقدم الخدمة',
                            'القسم الفرعي',
                            'السعر',
                            'تاريخ الإنشاء',
                        ],
                        fn ($record) => [
                            $record->id,
                            $record->service_ar,
                            $record->service_en,
                            $record->description_ar,
                            $record->description_en,
                            $record->provider?->title_ar ?? '',
                            $record->subCategory?->name_ar ?? '',
                            $record->price,
                            $record->created_at?->toDateTimeString() ?? '',
                        ]
                    ),
                ]),
            ]);
    }
}
