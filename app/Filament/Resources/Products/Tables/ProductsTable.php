<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        $isAr = app()->getLocale() === 'ar';

        // Dynamic headers & row callback for Excel Export based on active locale
        $exportHeaders = $isAr ? [
            'ID',
            'اسم المنتج (عربي)',
            'اسم المنتج (إنجليزي)',
            'السعر',
            'المخزون',
            'القسم الفرعي',
            'العلامة التجارية',
            'صاحب المنتج',
            'الحالة',
            'مميز؟',
            'تاريخ الإنشاء',
        ] : [
            'ID',
            'Product Name (Arabic)',
            'Product Name (English)',
            'Price',
            'Stock',
            'Sub Category',
            'Brand',
            'Owner / Provider',
            'Status',
            'Featured?',
            'Created At',
        ];

        $exportRowCallback = fn ($record) => $isAr ? [
            $record->id,
            $record->name_ar,
            $record->name_en,
            $record->price,
            $record->stock,
            $record->subCategory?->name_ar ?? '',
            $record->brand?->name_ar ?? '',
            $record->provider_id
                ? ($record->provider->title_ar ?? $record->provider->title_en ?? ('مقدم #' . $record->provider_id))
                : 'أدمن واصلي',
            $record->status,
            $record->is_featured ? 'نعم' : 'لا',
            $record->created_at?->toDateTimeString() ?? '',
        ] : [
            $record->id,
            $record->name_ar,
            $record->name_en,
            $record->price,
            $record->stock,
            $record->subCategory?->name_en ?: $record->subCategory?->name_ar ?: '',
            $record->brand?->name_en ?: $record->brand?->name_ar ?: '',
            $record->provider_id
                ? ($record->provider->title_en ?: $record->provider->title_ar ?: ('Provider #' . $record->provider_id))
                : 'Wasaly Admin',
            $record->status,
            $record->is_featured ? 'Yes' : 'No',
            $record->created_at?->toDateTimeString() ?? '',
        ];

        return $table
            ->columns([
                TextColumn::make('name_display')
                    ->label(__('messages.service_ar'))
                    ->state(fn ($record) => $isAr ? ($record->name_ar ?: $record->name_en) : ($record->name_en ?: $record->name_ar))
                    ->searchable(query: function ($query, $search) {
                        $query->where('name_ar', 'like', "%{$search}%")
                              ->orWhere('name_en', 'like', "%{$search}%");
                    }),
                ImageColumn::make('image')
                    ->label(__('messages.image'))
                    ->disk('public')
                    ->state(function ($record) {
                        if (!$record->image) return null;
                        return str_starts_with($record->image, 'products/') ? $record->image : 'products/' . $record->image;
                    })
                    ->circular(),
                TextColumn::make('price')
                    ->label(__('messages.price'))
                    ->money('EGP')
                    ->sortable(),
                TextColumn::make('stock')
                    ->label(__('messages.stock'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sub_category_display')
                    ->label(__('messages.sub_category'))
                    ->state(fn ($record) => $isAr 
                        ? ($record->subCategory?->name_ar ?: $record->subCategory?->name_en) 
                        : ($record->subCategory?->name_en ?: $record->subCategory?->name_ar)
                    )
                    ->sortable(),
                TextColumn::make('brand_display')
                    ->label(__('messages.brand'))
                    ->state(fn ($record) => $isAr 
                        ? ($record->brand?->name_ar ?: $record->brand?->name_en) 
                        : ($record->brand?->name_en ?: $record->brand?->name_ar)
                    )
                    ->sortable(),
                TextColumn::make('provider_owner')
                    ->label(__('messages.product_owner'))
                    ->state(fn ($record) => $record->provider_id
                        ? ($isAr
                            ? ($record->provider->title_ar ?? $record->provider->title_en ?? ('مقدم #' . $record->provider_id))
                            : ($record->provider->title_en ?? $record->provider->title_ar ?? ('Provider #' . $record->provider_id)))
                        : __('messages.admin_wasaly')
                    )
                    ->badge()
                    ->color(fn ($record): string => !$record->provider_id ? 'warning' : 'info'),
                TextColumn::make('status')
                    ->label(__('messages.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => __("messages.{$state}")),
                IconColumn::make('is_featured')
                    ->label(__('messages.is_featured'))
                    ->boolean(),
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
                    'products',
                    function (array $row) {
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

                        // Find or create brand (optional)
                        $brandName = $row['brand_ar'] ?? $row['brand'] ?? $row['العلامة_التجارية'] ?? '';
                        $brandId = null;
                        if ($brandName) {
                            $brand = \App\Models\Brand::where('name_ar', $brandName)->orWhere('name_en', $brandName)->first();
                            if (!$brand) {
                                $brand = \App\Models\Brand::create([
                                    'name_ar' => $brandName,
                                    'name_en' => $row['brand_en'] ?? $brandName,
                                    'image' => 'default.png',
                                    'status' => 'active',
                                ]);
                            }
                            $brandId = $brand->id;
                        }

                        // Find provider (optional)
                        $providerName = $row['provider_ar'] ?? $row['provider'] ?? $row['مقدم_الخدمة'] ?? '';
                        $providerId = null;
                        if ($providerName) {
                            $provider = \App\Models\Provider::where('title_ar', $providerName)->orWhere('title_en', $providerName)->first();
                            if ($provider) {
                                $providerId = $provider->id;
                            }
                        }

                        \App\Models\Product::create([
                            'name_ar' => $row['name_ar'] ?? $row['الاسم_عربي'] ?? '',
                            'name_en' => $row['name_en'] ?? $row['الاسم_إنجليزي'] ?? '',
                            'price' => floatval($row['price'] ?? $row['السعر'] ?? 0),
                            'stock' => intval($row['stock'] ?? $row['المخزون'] ?? 1),
                            'description_ar' => $row['description_ar'] ?? $row['الوصف_عربي'] ?? '',
                            'description_en' => $row['description_en'] ?? $row['الوصف_إنجليزي'] ?? '',
                            'sub_category_id' => $subCategoryId ?? 1,
                            'brand_id' => $brandId,
                            'provider_id' => $providerId,
                            'status' => $row['status'] ?? $row['الحالة'] ?? 'active',
                            'is_featured' => filter_var($row['is_featured'] ?? $row['مميز'] ?? false, FILTER_VALIDATE_BOOLEAN),
                            'image' => 'default.png',
                        ]);
                    }
                ),
                \App\Helpers\FilamentExportHelper::makeExportHeaderAction(
                    'products',
                    $exportHeaders,
                    $exportRowCallback,
                    \App\Models\Product::class
                )
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    \App\Helpers\FilamentExportHelper::makeExportBulkAction(
                        'products',
                        $exportHeaders,
                        $exportRowCallback
                    ),
                ]),
            ]);
    }
}
