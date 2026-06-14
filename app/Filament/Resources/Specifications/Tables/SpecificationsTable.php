<?php

namespace App\Filament\Resources\Specifications\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SpecificationsTable
{
    public static function configure(Table $table): Table
    {
        $isAr = app()->getLocale() === 'ar';

        $exportHeaders = $isAr ? [
            'ID',
            'المنتج',
            'الخاصية (عربي)',
            'الخاصية (إنجليزي)',
            'القيمة (عربي)',
            'القيمة (إنجليزي)',
            'تاريخ الإنشاء',
        ] : [
            'ID',
            'Product',
            'Specification (Arabic)',
            'Specification (English)',
            'Value (Arabic)',
            'Value (English)',
            'Created At',
        ];

        $exportRowCallback = fn ($record) => $isAr ? [
            $record->id,
            $record->product?->name_ar ?? $record->product?->name_en ?? '',
            $record->key_ar,
            $record->key_en,
            $record->value_ar,
            $record->value_en,
            $record->created_at?->toDateTimeString() ?? '',
        ] : [
            $record->id,
            $record->product?->name_en ?: $record->product?->name_ar ?: '',
            $record->key_ar,
            $record->key_en,
            $record->value_ar,
            $record->value_en,
            $record->created_at?->toDateTimeString() ?? '',
        ];

        return $table
            ->columns([
                ImageColumn::make('icon')
                    ->label(__('messages.icon'))
                    ->state(fn ($record) => $record->icon_path)
                    ->circular(),

                TextColumn::make('product.name_ar')
                    ->label(__('messages.product'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('key_ar')
                    ->label(__('messages.key_ar'))
                    ->searchable(),

                TextColumn::make('value_ar')
                    ->label(__('messages.value_ar'))
                    ->searchable(),

                TextColumn::make('key_en')
                    ->label(__('messages.key_en'))
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('value_en')
                    ->label(__('messages.value_en'))
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('product_id')
                    ->label(__('messages.product'))
                    ->relationship('product', 'name_ar')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                \App\Helpers\FilamentExportHelper::makeImportHeaderAction(
                    'specifications',
                    function (array $row) {
                        // Find product
                        $productName = $row['product'] ?? $row['المنتج'] ?? $row['اسم_المنتج'] ?? $row['اسم_المنتج_عربي'] ?? $row['الاسم_عربي'] ?? '';
                        $product = null;
                        if ($productName) {
                            $product = \App\Models\Product::where('name_ar', $productName)->orWhere('name_en', $productName)->first();
                        }

                        if (!$product) {
                            throw new \Exception(app()->getLocale() == 'ar' 
                                ? "المنتج '{$productName}' غير موجود في النظام." 
                                : "Product '{$productName}' not found.");
                        }

                        \App\Models\Specification::create([
                            'product_id' => $product->id,
                            'key_ar' => $row['key_ar'] ?? $row['الخاصية_عربي'] ?? $row['الاسم_عربي'] ?? $row['الخاصية'] ?? $row['الاسم'] ?? '',
                            'key_en' => $row['key_en'] ?? $row['الخاصية_إنجليزي'] ?? $row['الاسم_إنجليزي'] ?? '',
                            'value_ar' => $row['value_ar'] ?? $row['القيمة_عربي'] ?? $row['القيمة'] ?? '',
                            'value_en' => $row['value_en'] ?? $row['القيمة_إنجليزي'] ?? '',
                            'icon' => null,
                        ]);
                    }
                ),
                \App\Helpers\FilamentExportHelper::makeExportHeaderAction(
                    'specifications',
                    $exportHeaders,
                    $exportRowCallback,
                    \App\Models\Specification::class
                )
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    \App\Helpers\FilamentExportHelper::makeExportBulkAction(
                        'specifications',
                        $exportHeaders,
                        $exportRowCallback
                    ),
                ]),
            ]);
    }
}
