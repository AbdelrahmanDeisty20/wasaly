<?php

namespace App\Filament\Resources\Providers\Tables;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class ProvidersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover')
                    ->label(__('messages.image'))
                    ->disk('public')
                    ->state(function ($record) {
                        if (!$record->cover) return null;
                        return str_starts_with($record->cover, 'providers/') ? $record->cover : 'providers/' . $record->cover;
                    })
                    ->circular(),
                TextColumn::make('title_ar')
                    ->label(__('messages.service_ar'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('messages.user'))
                    ->searchable(),
                TextColumn::make('subCategory.name_ar')
                    ->label(__('messages.sub_category'))
                    ->badge()
                    ->color('info'),
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
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    \App\Helpers\FilamentExportHelper::makeExportBulkAction(
                        'providers',
                        [
                            'ID',
                            'الاسم (عربي)',
                            'الاسم (إنجليزي)',
                            'المستخدم المرتبط',
                            'القسم الفرعي',
                            'الوصف (عربي)',
                            'الوصف (إنجليزي)',
                            'السعر يبدأ من',
                            'أيام العمل',
                            'وقت العمل',
                            'الحالة',
                            'تاريخ الإنشاء',
                        ],
                        fn ($record) => [
                            $record->id,
                            $record->title_ar,
                            $record->title_en,
                            $record->user?->full_name ?? '',
                            $record->subCategory?->name_ar ?? '',
                            $record->service_description_ar,
                            $record->service_description_en,
                            $record->price_from,
                            ($record->from_day . ' - ' . $record->to_day),
                            ($record->start_time . ' - ' . $record->end_time),
                            $record->status,
                            $record->created_at?->toDateTimeString() ?? '',
                        ]
                    ),
                ]),
            ]);
    }
}
