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
        $isAr = app()->getLocale() === 'ar';

        // Dynamic headers & row callback for Excel Export based on active locale
        $exportHeaders = $isAr ? [
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
        ] : [
            'ID',
            'Provider Title (Arabic)',
            'Provider Title (English)',
            'Linked User',
            'Sub Category',
            'Description (Arabic)',
            'Description (English)',
            'Price Starts From',
            'Working Days',
            'Working Hours',
            'Status',
            'Created At',
        ];

        $exportRowCallback = fn ($record) => $isAr ? [
            $record->id,
            $record->title_ar,
            $record->title_en,
            $record->user?->full_name ?? '',
            $record->subCategory?->name_ar ?? '',
            $record->service_description_ar,
            $record->service_description_en,
            $record->price_from,
            "من {$record->from_day} إلى {$record->to_day}",
            "من {$record->start_time} إلى {$record->end_time}",
            $record->status,
            $record->created_at?->toDateTimeString() ?? '',
        ] : [
            $record->id,
            $record->title_ar,
            $record->title_en,
            $record->user?->full_name ?? '',
            $record->subCategory?->name_en ?: $record->subCategory?->name_ar ?: '',
            $record->service_description_ar,
            $record->service_description_en,
            $record->price_from,
            "From {$record->from_day} to {$record->to_day}",
            "From {$record->start_time} to {$record->end_time}",
            $record->status,
            $record->created_at?->toDateTimeString() ?? '',
        ];

        return $table
            ->columns([
                ImageColumn::make('cover')
                    ->label(__('messages.image'))
                    ->state(fn ($record) => $record->image_path)
                    ->circular(),
                TextColumn::make('title_display')
                    ->label(__('messages.service_ar'))
                    ->state(fn ($record) => $isAr ? ($record->title_ar ?: $record->title_en) : ($record->title_en ?: $record->title_ar))
                    ->searchable(query: function ($query, $search) {
                        $query->where('title_ar', 'like', "%{$search}%")
                              ->orWhere('title_en', 'like', "%{$search}%");
                    })
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('messages.user'))
                    ->searchable(),
                TextColumn::make('sub_category_display')
                    ->label(__('messages.sub_category'))
                    ->state(fn ($record) => $isAr 
                        ? ($record->subCategory?->name_ar ?: $record->subCategory?->name_en) 
                        : ($record->subCategory?->name_en ?: $record->subCategory?->name_ar)
                    )
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
            ->headerActions([
                \App\Helpers\FilamentExportHelper::makeImportHeaderAction(
                    'providers',
                    function (array $row) {
                        // Find or create User
                        $userName = $row['user_name'] ?? $row['اسم_المستخدم'] ?? $row['المستخدم_المرتبط'] ?? $row['المستخدم'] ?? '';
                        $userEmail = $row['user_email'] ?? $row['البريد_الإلكتروني'] ?? '';
                        $user = null;
                        
                        if ($userEmail) {
                            $user = \App\Models\User::where('email', $userEmail)->first();
                        }
                        
                        if (!$user && $userName) {
                            $user = \App\Models\User::where('full_name', $userName)->orWhere('name', $userName)->first();
                        }
                        
                        if (!$user) {
                            $user = \App\Models\User::create([
                                'full_name' => $userName ?: 'مقدم جديد',
                                'name' => $userName ?: 'مقدم جديد',
                                'email' => $userEmail ?: 'provider_' . uniqid() . '@wasaly.com',
                                'phone' => $row['user_phone'] ?? $row['هاتف_المستخدم'] ?? '',
                                'type' => 'service_provider',
                                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                                'is_active' => true,
                            ]);
                        }

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

                        \App\Models\Provider::create([
                            'user_id' => $user->id,
                            'sub_category_id' => $subCategoryId ?? 1,
                            'title_ar' => $row['title_ar'] ?? $row['الاسم_عربي'] ?? '',
                            'title_en' => $row['title_en'] ?? $row['الاسم_إنجليزي'] ?? '',
                            'service_description_ar' => $row['service_description_ar'] ?? $row['الوصف_عربي'] ?? '',
                            'service_description_en' => $row['service_description_en'] ?? $row['الوصف_إنجليزي'] ?? '',
                            'price_from' => floatval($row['price_from'] ?? $row['السعر_يبدأ_من'] ?? 0),
                            'from_day' => $row['from_day'] ?? 'Saturday',
                            'to_day' => $row['to_day'] ?? 'Thursday',
                            'start_time' => $row['start_time'] ?? '09:00:00',
                            'end_time' => $row['end_time'] ?? '21:00:00',
                            'status' => $row['status'] ?? $row['الحالة'] ?? 'active',
                            'cover' => 'default.png',
                        ]);
                    }
                ),
                \App\Helpers\FilamentExportHelper::makeExportHeaderAction(
                    'providers',
                    $exportHeaders,
                    $exportRowCallback,
                    \App\Models\Provider::class
                )
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    \App\Helpers\FilamentExportHelper::makeExportBulkAction(
                        'providers',
                        $exportHeaders,
                        $exportRowCallback
                    ),
                ]),
            ]);
    }
}
