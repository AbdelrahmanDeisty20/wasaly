<?php

namespace App\Filament\Resources\ActivityLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class ActivityLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $isAr = app()->getLocale() == 'ar';

        return $schema
            ->columns(1)
            ->components([
                Section::make($isAr ? 'تفاصيل العملية' : 'Activity Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('id')
                                    ->label('ID'),
                                
                                TextEntry::make('log_name')
                                    ->label($isAr ? 'نوع السجل' : 'Log Name')
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('event')
                                    ->label($isAr ? 'العملية' : 'Event')
                                    ->badge()
                                    ->color(fn (?string $state): string => match ($state) {
                                        'created' => 'success',
                                        'updated' => 'warning',
                                        'deleted' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                                        'created' => $isAr ? 'إضافة' : 'Created',
                                        'updated' => $isAr ? 'تعديل' : 'Updated',
                                        'deleted' => $isAr ? 'حذف' : 'Deleted',
                                        default => $state ?? '',
                                    }),

                                TextEntry::make('created_at')
                                    ->label($isAr ? 'تاريخ ووقت العملية' : 'Date & Time')
                                    ->dateTime(),

                                TextEntry::make('subject_type')
                                    ->label($isAr ? 'نوع العنصر' : 'Subject Type')
                                    ->formatStateUsing(fn (string $state) => class_basename($state)),

                                TextEntry::make('subject_id')
                                    ->label($isAr ? 'معرف العنصر' : 'Subject ID'),

                                TextEntry::make('causer.full_name')
                                    ->label($isAr ? 'القائم بالعملية' : 'Performed By')
                                    ->placeholder('System / System Admin')
                                    ->columnSpanFull(),
                                
                                TextEntry::make('description')
                                    ->label($isAr ? 'الوصف' : 'Description')
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Section::make($isAr ? 'التغييرات والتفاصيل' : 'Changes & Details')
                    ->schema([
                        TextEntry::make('properties')
                            ->hiddenLabel()
                            ->html()
                            ->formatStateUsing(function ($state) use ($isAr) {
                                if (empty($state)) {
                                    return '<div class="text-gray-400 text-sm">' . ($isAr ? 'لا توجد تغييرات مسجلة لهذه العملية.' : 'No changes recorded for this activity.') . '</div>';
                                }
                                
                                $html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">';
                                
                                // Let's format old values (if any)
                                if (isset($state['old']) && is_array($state['old'])) {
                                    $html .= '<div class="p-4 bg-danger-50/20 border border-danger-200 dark:border-danger-900/50 rounded-xl">';
                                    $html .= '<h4 class="font-bold text-sm text-danger-600 dark:text-danger-400 mb-4 flex items-center gap-2">' . ($isAr ? '🔴 القيم السابقة' : '🔴 Previous Values') . '</h4>';
                                    $html .= '<div class="overflow-x-auto"><table class="w-full text-right md:text-left text-xs">';
                                    $html .= '<thead><tr class="border-b border-gray-200 dark:border-gray-700/50"><th class="pb-2 font-medium text-gray-500">' . ($isAr ? 'الحقل' : 'Field') . '</th><th class="pb-2 font-medium text-gray-500">' . ($isAr ? 'القيمة' : 'Value') . '</th></tr></thead>';
                                    $html .= '<tbody class="divide-y divide-gray-100 dark:divide-gray-800/30">';
                                    foreach ($state['old'] as $key => $value) {
                                        $displayValue = is_array($value) || is_object($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
                                        $html .= '<tr><td class="py-2 font-mono font-medium text-gray-700 dark:text-gray-300">' . e($key) . '</td><td class="py-2 text-gray-600 dark:text-gray-400 break-all">' . e($displayValue) . '</td></tr>';
                                    }
                                    $html .= '</tbody></table></div></div>';
                                }

                                // Let's format modified attributes (new/updated values)
                                if (isset($state['attributes']) && is_array($state['attributes'])) {
                                    $html .= '<div class="p-4 bg-success-50/20 border border-success-200 dark:border-success-900/50 rounded-xl">';
                                    $html .= '<h4 class="font-bold text-sm text-success-600 dark:text-success-400 mb-4 flex items-center gap-2">' . ($isAr ? '🟢 القيم الجديدة' : '🟢 New Values') . '</h4>';
                                    $html .= '<div class="overflow-x-auto"><table class="w-full text-right md:text-left text-xs">';
                                    $html .= '<thead><tr class="border-b border-gray-200 dark:border-gray-700/50"><th class="pb-2 font-medium text-gray-500">' . ($isAr ? 'الحقل' : 'Field') . '</th><th class="pb-2 font-medium text-gray-500">' . ($isAr ? 'القيمة' : 'Value') . '</th></tr></thead>';
                                    $html .= '<tbody class="divide-y divide-gray-100 dark:divide-gray-800/30">';
                                    foreach ($state['attributes'] as $key => $value) {
                                        $displayValue = is_array($value) || is_object($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
                                        $html .= '<tr><td class="py-2 font-mono font-medium text-gray-700 dark:text-gray-300">' . e($key) . '</td><td class="py-2 text-gray-600 dark:text-gray-400 break-all">' . e($displayValue) . '</td></tr>';
                                    }
                                    $html .= '</tbody></table></div></div>';
                                }

                                // If it's a generic properties array not matching 'attributes' / 'old'
                                if (!isset($state['attributes']) && !isset($state['old'])) {
                                    $html .= '<div class="col-span-full"><pre class="text-xs p-4 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-xl font-mono overflow-auto text-gray-700 dark:text-gray-300">' . e(json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre></div>';
                                }

                                $html .= '</div>';
                                return $html;
                            })
                            ->columnSpanFull(),
                    ])
            ]);
    }
}
