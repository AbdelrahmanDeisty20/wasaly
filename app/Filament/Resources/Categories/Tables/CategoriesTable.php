<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        $isAr = app()->getLocale() === 'ar';

        // Dynamic headers & row callback for Excel Export based on active locale
        $exportHeaders = $isAr ? [
            'ID',
            'الاسم بالعربية',
            'الاسم بالإنجليزية',
            'عدد الأقسام الفرعية',
            'الحالة',
            'تاريخ الإنشاء',
        ] : [
            'ID',
            'Category Name (Arabic)',
            'Category Name (English)',
            'Sub Categories Count',
            'Status',
            'Created At',
        ];

        $exportRowCallback = fn ($record) => [
            $record->id,
            $record->name_ar,
            $record->name_en,
            $record->subCategories()->count(),
            $record->status,
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
                        return str_starts_with($record->image, 'categories/') ? $record->image : 'categories/' . $record->image;
                    })
                    ->circular(),
                TextColumn::make('sub_categories_count')
                    ->label(__('messages.sub_categories'))
                    ->counts('subCategories')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('messages.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => __("messages.{$state}")),
                TextColumn::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                \App\Helpers\FilamentExportHelper::makeImportHeaderAction(
                    'categories',
                    function (array $row) {
                        \App\Models\Category::create([
                            'name_ar' => $row['name_ar'] ?? $row['الاسم_بالعربية'] ?? '',
                            'name_en' => $row['name_en'] ?? $row['الاسم_بالإنجليزية'] ?? '',
                            'status' => $row['status'] ?? $row['الحالة'] ?? 'active',
                            'image' => 'default.png',
                        ]);
                    }
                ),
                \App\Helpers\FilamentExportHelper::makeExportHeaderAction(
                    'categories',
                    $exportHeaders,
                    $exportRowCallback,
                    \App\Models\Category::class
                )
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    \App\Helpers\FilamentExportHelper::makeExportBulkAction(
                        'categories',
                        $exportHeaders,
                        $exportRowCallback
                    ),
                ]),
            ]);
    }
}
