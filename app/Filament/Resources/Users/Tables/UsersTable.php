<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\ImageColumn::make('avatar')
                    ->label(__('messages.avatar_required'))
                    ->disk('public')
                    ->state(function ($record) {
                        if (!$record->avatar) return null;
                        return str_starts_with($record->avatar, 'avatars/') ? $record->avatar : 'avatars/' . $record->avatar;
                    })
                    ->circular(),
                TextColumn::make('name')
                    ->label(__('messages.user'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('messages.email_nullable'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(__('messages.phone_nullable'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(__('messages.type_required'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'user' => 'success',
                        'service_provider' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => __("messages.{$state}")),
                IconColumn::make('is_active')
                    ->label(__('messages.active'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('type')
                    ->label(app()->getLocale() == 'ar' ? 'نوع الحساب' : 'Account Type')
                    ->options([
                        'user' => app()->getLocale() == 'ar' ? 'عميل عادي' : 'Customer',
                        'service_provider' => app()->getLocale() == 'ar' ? 'مقدم خدمة' : 'Service Provider',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                \App\Helpers\FilamentExportHelper::makeImportHeaderAction(
                    'users',
                    function (array $row) {
                        $type = $row['type'] ?? $row['نوع_الحساب'] ?? 'user';
                        // Map type names
                        if ($type === 'عميل عادي' || $type === 'عميل') {
                            $type = 'user';
                        } elseif ($type === 'مقدم خدمة' || $type === 'مقدم_خدمة') {
                            $type = 'service_provider';
                        }

                        \App\Models\User::create([
                            'full_name' => $row['full_name'] ?? $row['الاسم_الكامل'] ?? '',
                            'name' => $row['name'] ?? $row['الاسم_المختصر'] ?? $row['full_name'] ?? '',
                            'email' => $row['email'] ?? $row['البريد_الإلكتروني'] ?? '',
                            'phone' => $row['phone'] ?? $row['الهاتف'] ?? '',
                            'type' => $type,
                            'password' => \Illuminate\Support\Facades\Hash::make($row['password'] ?? 'password'),
                            'is_active' => filter_var($row['is_active'] ?? $row['نشط'] ?? true, FILTER_VALIDATE_BOOLEAN),
                            'avatar' => null,
                        ]);
                    }
                )
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    \App\Helpers\FilamentExportHelper::makeExportBulkAction(
                        'users',
                        [
                            'ID',
                            'الاسم الكامل',
                            'البريد الإلكتروني',
                            'الهاتف',
                            'نوع الحساب',
                            'هل الحساب نشط؟',
                            'تاريخ الإنشاء',
                        ],
                        fn ($record) => [
                            $record->id,
                            $record->full_name,
                            $record->email,
                            $record->phone,
                            $record->type === 'user' ? 'عميل عادي' : ($record->type === 'service_provider' ? 'مقدم خدمة' : $record->type),
                            $record->is_active ? 'نعم' : 'لا',
                            $record->created_at?->toDateTimeString() ?? '',
                        ]
                    ),
                ]),
            ]);
    }
}
