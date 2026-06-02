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
        return $table
            ->columns([
                TextColumn::make('name_ar')
                    ->label(__('messages.service_ar'))
                    ->searchable(),
                TextColumn::make('name_en')
                    ->label(__('messages.service_en'))
                    ->searchable(),
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
                TextColumn::make('updated_at')
                    ->label(__('messages.updated_at'))
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
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    \App\Helpers\FilamentExportHelper::makeExportBulkAction(
                        'categories',
                        [
                            'ID',
                            'الاسم بالعربية',
                            'الاسم بالإنجليزية',
                            'عدد الأقسام الفرعية',
                            'الحالة',
                            'تاريخ الإنشاء',
                        ],
                        fn ($record) => [
                            $record->id,
                            $record->name_ar,
                            $record->name_en,
                            $record->subCategories()->count(),
                            $record->status,
                            $record->created_at?->toDateTimeString() ?? '',
                        ]
                    ),
                ]),
            ]);
    }
}
