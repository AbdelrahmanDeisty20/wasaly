<?php

namespace App\Filament\Resources\Offers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OffersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name_ar')
                    ->label(app()->getLocale() == 'ar' ? 'المنتج' : 'Product')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('discount_percentage')
                    ->label(app()->getLocale() == 'ar' ? 'نسبة الخصم' : 'Discount')
                    ->badge()
                    ->color('success')
                    ->suffix('%')
                    ->sortable(),

                TextColumn::make('start_date')
                    ->label(app()->getLocale() == 'ar' ? 'تاريخ البداية' : 'Start Date')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label(app()->getLocale() == 'ar' ? 'تاريخ الانتهاء' : 'End Date')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(app()->getLocale() == 'ar' ? 'نشط' : 'Active')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->label(app()->getLocale() == 'ar' ? 'تاريخ الإضافة' : 'Created At')
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label(app()->getLocale() == 'ar' ? 'الحالة' : 'Status')
                    ->options([
                        '1' => app()->getLocale() == 'ar' ? 'نشط' : 'Active',
                        '0' => app()->getLocale() == 'ar' ? 'غير نشط' : 'Inactive',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
