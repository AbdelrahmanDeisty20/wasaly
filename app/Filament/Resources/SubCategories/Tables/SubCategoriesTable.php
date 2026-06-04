<?php

namespace App\Filament\Resources\SubCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubCategoriesTable
{
    public static function configure(Table $table): Table
    {
        $isAr = app()->getLocale() === 'ar';

        return $table
            ->columns([

                // الصورة — دائرية وأكبر
                ImageColumn::make('image')
                    ->label('')
                    ->state(fn ($record) => $record->image_path)
                    ->circular()
                    ->size(52),

                // الاسم العربي كـ header مع الإنجليزي كـ description والعكس
                TextColumn::make('name_display')
                    ->label(__('messages.service_ar'))
                    ->state(fn ($record) => $isAr ? ($record->name_ar ?: $record->name_en) : ($record->name_en ?: $record->name_ar))
                    ->description(fn ($record): string => $isAr ? ($record->name_en ?? '') : ($record->name_ar ?? ''))
                    ->weight('bold')
                    ->searchable(query: function ($query, $search) {
                        $query->where('name_ar', 'like', "%{$search}%")
                              ->orWhere('name_en', 'like', "%{$search}%");
                    })
                    ->sortable(),

                // الحالة badge
                TextColumn::make('status')
                    ->label(__('messages.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'   => 'success',
                        'inactive' => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => __("messages.{$state}")),

                // تاريخ الإضافة
                TextColumn::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime('d M Y')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
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
                ]),
            ]);
    }
}
