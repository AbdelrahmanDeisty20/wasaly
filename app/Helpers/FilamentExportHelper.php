<?php

namespace App\Helpers;

use Filament\Actions\BulkAction;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FilamentExportHelper
{
    /**
     * Generate a reusable bulk action to export table records to a high-quality CSV (fully Excel compatible).
     */
    public static function makeExportBulkAction(string $fileName, array $headers, callable $rowCallback): BulkAction
    {
        $labelAr = 'تصدير إلى إكسيل';
        $labelEn = 'Export to Excel';
        $label = app()->getLocale() == 'ar' ? $labelAr : $labelEn;

        return BulkAction::make('export_excel')
            ->label($label)
            ->icon('heroicon-o-document-arrow-down')
            ->color('success')
            ->action(function (Collection $records) use ($fileName, $headers, $rowCallback): StreamedResponse {
                $response = new StreamedResponse(function () use ($records, $headers, $rowCallback) {
                    $handle = fopen('php://output', 'w');
                    
                    // Prepend BOM to force Excel to read Arabic text (UTF-8) correctly
                    fwrite($handle, "\xEF\xBB\xBF");
                    
                    // Add CSV Headers
                    fputcsv($handle, $headers);
                    
                    // Add CSV Rows
                    foreach ($records as $record) {
                        $row = $rowCallback($record);
                        fputcsv($handle, $row);
                    }
                    
                    fclose($handle);
                });

                $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
                $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '_' . date('Y-m-d_H-i-s') . '.csv"');
                
                return $response;
            });
    }

    /**
     * Generate a reusable header action to export ALL table records directly to Excel/CSV.
     */
    public static function makeExportHeaderAction(string $fileName, array $headers, callable $rowCallback, string $modelClass): Action
    {
        $labelAr = 'تصدير إلى إكسيل';
        $labelEn = 'Export to Excel';
        $label = app()->getLocale() == 'ar' ? $labelAr : $labelEn;

        return Action::make('export_excel_all')
            ->label($label)
            ->icon('heroicon-o-document-arrow-down')
            ->color('success')
            ->action(function () use ($fileName, $headers, $rowCallback, $modelClass): StreamedResponse {
                $records = $modelClass::all();

                $response = new StreamedResponse(function () use ($records, $headers, $rowCallback) {
                    $handle = fopen('php://output', 'w');
                    
                    // Prepend BOM to force Excel to read Arabic text (UTF-8) correctly
                    fwrite($handle, "\xEF\xBB\xBF");
                    
                    // Add CSV Headers
                    fputcsv($handle, $headers);
                    
                    // Add CSV Rows
                    foreach ($records as $record) {
                        $row = $rowCallback($record);
                        fputcsv($handle, $row);
                    }
                    
                    fclose($handle);
                });

                $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
                $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '_all_' . date('Y-m-d_H-i-s') . '.csv"');
                
                return $response;
            });
    }

    /**
     * Generate a reusable header action to import records from a CSV/Excel file.
     */
    public static function makeImportHeaderAction(string $resourceName, callable $importCallback): Action
    {
        $labelAr = 'استيراد من إكسيل / CSV';
        $labelEn = 'Import from Excel / CSV';
        $label = app()->getLocale() == 'ar' ? $labelAr : $labelEn;

        $instructionsHtml = '';
        if (app()->getLocale() == 'ar') {
            $instructionsHtml .= '<div style="background-color: #1e1e2e; padding: 16px; border-radius: 8px; border-left: 4px solid #10b981; margin-bottom: 12px; font-family: sans-serif;">';
            $instructionsHtml .= '<h4 style="font-weight: bold; color: #10b981; margin: 0 0 8px 0; font-size: 14px; display: flex; align-items: center; gap: 6px;">';
            $instructionsHtml .= '<svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            $instructionsHtml .= 'تعليمات شيت الإكسيل / CSV للاستيراد:</h4>';
            $instructionsHtml .= '<p style="font-size: 12px; color: #d1d1d6; margin: 0 0 10px 0; line-height: 1.5;">يرجى رفع ملف بصيغة <strong>Excel (.xlsx / .xls)</strong> أو <strong>CSV</strong> يحتوي على أسماء الأعمدة التالية تماماً (في الصف الأول):</p>';
            
            if ($resourceName === 'products') {
                $instructionsHtml .= '<code style="background: #2d2d3f; padding: 6px 10px; border-radius: 6px; color: #34d399; font-size: 11px; display: block; word-break: break-all; font-family: monospace; border: 1px solid #3f3f56; direction: ltr; text-align: left;">name_ar, name_en, price, stock, description_ar, description_en, subcategory_ar, brand_ar, provider_ar</code>';
                $instructionsHtml .= '<ul style="list-style-type: disc; margin: 10px 18px 0 0; padding: 0; font-size: 11px; color: #a1a1aa; line-height: 1.6;">';
                $instructionsHtml .= '<li><strong>subcategory_ar:</strong> اسم القسم الفرعي (إذا لم يكن موجوداً، سيتم إنشاؤه تلقائياً).</li>';
                $instructionsHtml .= '<li><strong>brand_ar:</strong> العلامة التجارية (اختياري، وسيتم إنشاؤها تلقائياً إن لم تكن موجودة).</li>';
                $instructionsHtml .= '<li><strong>provider_ar:</strong> اسم مقدم الخدمة لربط المنتج به.</li>';
                $instructionsHtml .= '</ul>';
            } elseif ($resourceName === 'categories') {
                $instructionsHtml .= '<code style="background: #2d2d3f; padding: 6px 10px; border-radius: 6px; color: #34d399; font-size: 11px; display: block; word-break: break-all; font-family: monospace; border: 1px solid #3f3f56; direction: ltr; text-align: left;">name_ar, name_en, status</code>';
                $instructionsHtml .= '<p style="font-size: 11px; color: #a1a1aa; margin: 6px 0 0 0;">* حقل <strong>status</strong> يقبل القيمة <code>active</code> (نشط) أو <code>inactive</code> (غير نشط).</p>';
            } elseif ($resourceName === 'users') {
                $instructionsHtml .= '<code style="background: #2d2d3f; padding: 6px 10px; border-radius: 6px; color: #34d399; font-size: 11px; display: block; word-break: break-all; font-family: monospace; border: 1px solid #3f3f56; direction: ltr; text-align: left;">full_name, email, phone, type, password</code>';
                $instructionsHtml .= '<ul style="list-style-type: disc; margin: 10px 18px 0 0; padding: 0; font-size: 11px; color: #a1a1aa; line-height: 1.6;">';
                $instructionsHtml .= '<li><strong>type:</strong> نوع الحساب، يقبل <code>user</code> (عميل) أو <code>service_provider</code> (مقدم خدمة).</li>';
                $instructionsHtml .= '<li><strong>password:</strong> سيتم تشفير كلمة المرور وحمايتها تلقائياً.</li>';
                $instructionsHtml .= '</ul>';
            } elseif ($resourceName === 'providers') {
                $instructionsHtml .= '<code style="background: #2d2d3f; padding: 6px 10px; border-radius: 6px; color: #34d399; font-size: 11px; display: block; word-break: break-all; font-family: monospace; border: 1px solid #3f3f56; direction: ltr; text-align: left;">title_ar, title_en, user_email, subcategory_ar, price_from, service_description_ar</code>';
                $instructionsHtml .= '<ul style="list-style-type: disc; margin: 10px 18px 0 0; padding: 0; font-size: 11px; color: #a1a1aa; line-height: 1.6;">';
                $instructionsHtml .= '<li><strong>user_email:</strong> البريد الإلكتروني للمستخدم. إذا لم يكن موجوداً، سيتم إنشاء حساب مستخدم له تلقائياً!</li>';
                $instructionsHtml .= '<li><strong>subcategory_ar:</strong> القسم الفرعي لربط مقدم الخدمة به.</li>';
                $instructionsHtml .= '</ul>';
            } elseif ($resourceName === 'services') {
                $instructionsHtml .= '<code style="background: #2d2d3f; padding: 6px 10px; border-radius: 6px; color: #34d399; font-size: 11px; display: block; word-break: break-all; font-family: monospace; border: 1px solid #3f3f56; direction: ltr; text-align: left;">service_ar, service_en, price, provider_ar, subcategory_ar</code>';
                $instructionsHtml .= '<ul style="list-style-type: disc; margin: 10px 18px 0 0; padding: 0; font-size: 11px; color: #a1a1aa; line-height: 1.6;">';
                $instructionsHtml .= '<li><strong>provider_ar:</strong> اسم مقدم الخدمة المسجل لربط الخدمة به.</li>';
                $instructionsHtml .= '<li><strong>subcategory_ar:</strong> القسم الفرعي لربط الخدمة به.</li>';
                $instructionsHtml .= '</ul>';
            }
            $instructionsHtml .= '</div>';
        } else {
            $instructionsHtml .= '<div style="background-color: #1e1e2e; padding: 16px; border-radius: 8px; border-left: 4px solid #3b82f6; margin-bottom: 12px; font-family: sans-serif;">';
            $instructionsHtml .= '<h4 style="font-weight: bold; color: #3b82f6; margin: 0 0 8px 0; font-size: 14px; display: flex; align-items: center; gap: 6px;">';
            $instructionsHtml .= '<svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            $instructionsHtml .= 'Excel / CSV Import Instructions:</h4>';
            $instructionsHtml .= '<p style="font-size: 12px; color: #d1d1d6; margin: 0 0 10px 0; line-height: 1.5;">Please upload an <strong>Excel (.xlsx / .xls)</strong> or <strong>CSV</strong> file with exactly these header names in the first row:</p>';
            
            if ($resourceName === 'products') {
                $instructionsHtml .= '<code style="background: #2d2d3f; padding: 6px 10px; border-radius: 6px; color: #60a5fa; font-size: 11px; display: block; word-break: break-all; font-family: monospace; border: 1px solid #3f3f56;">name_ar, name_en, price, stock, description_ar, description_en, subcategory_ar, brand_ar, provider_ar</code>';
            } elseif ($resourceName === 'categories') {
                $instructionsHtml .= '<code style="background: #2d2d3f; padding: 6px 10px; border-radius: 6px; color: #60a5fa; font-size: 11px; display: block; word-break: break-all; font-family: monospace; border: 1px solid #3f3f56;">name_ar, name_en, status</code>';
            } elseif ($resourceName === 'users') {
                $instructionsHtml .= '<code style="background: #2d2d3f; padding: 6px 10px; border-radius: 6px; color: #60a5fa; font-size: 11px; display: block; word-break: break-all; font-family: monospace; border: 1px solid #3f3f56;">full_name, email, phone, type, password</code>';
            } elseif ($resourceName === 'providers') {
                $instructionsHtml .= '<code style="background: #2d2d3f; padding: 6px 10px; border-radius: 6px; color: #60a5fa; font-size: 11px; display: block; word-break: break-all; font-family: monospace; border: 1px solid #3f3f56;">title_ar, title_en, user_email, subcategory_ar, price_from, service_description_ar</code>';
            } elseif ($resourceName === 'services') {
                $instructionsHtml .= '<code style="background: #2d2d3f; padding: 6px 10px; border-radius: 6px; color: #60a5fa; font-size: 11px; display: block; word-break: break-all; font-family: monospace; border: 1px solid #3f3f56;">service_ar, service_en, price, provider_ar, subcategory_ar</code>';
            }
            $instructionsHtml .= '</div>';
        }

        return Action::make('import_excel')
            ->label($label)
            ->icon('heroicon-o-document-arrow-up')
            ->color('info')
            ->form([
                \Filament\Forms\Components\Placeholder::make('import_instructions')
                    ->label('')
                    ->content(new \Illuminate\Support\HtmlString($instructionsHtml)),

                FileUpload::make('file')
                    ->label(app()->getLocale() == 'ar' ? 'ملف Excel / CSV' : 'Excel / CSV File')
                    ->acceptedFileTypes([
                        'text/csv',
                        'text/plain',
                        'application/csv',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    ])
                    ->required()
                    ->disk('public')
                    ->directory('imports'),
            ])
            ->action(function (array $data) use ($importCallback) {
                $filePath = storage_path('app/public/' . $data['file']);
                
                if (!file_exists($filePath)) {
                    \Filament\Notifications\Notification::make()
                        ->title(app()->getLocale() == 'ar' ? 'خطأ في الملف' : 'File Error')
                        ->body(app()->getLocale() == 'ar' ? 'لم يتم العثور على الملف المرفوع.' : 'The uploaded file was not found.')
                        ->danger()
                        ->send();
                    return;
                }

                try {
                    $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($filePath);
                } catch (\Exception $e) {
                    $inputFileType = null;
                }

                if ($inputFileType === 'Csv') {
                    try {
                        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                        
                        // Detect encoding
                        $fileContent = file_get_contents($filePath);
                        if (!mb_check_encoding($fileContent, 'UTF-8')) {
                            $reader->setInputEncoding('CP1256');
                        } else {
                            $reader->setInputEncoding('UTF-8');
                        }

                        // Detect delimiter
                        $delimiter = ',';
                        $firstLine = fgets(fopen($filePath, 'r'));
                        if ($firstLine !== false) {
                            $numCommas = substr_count($firstLine, ',');
                            $numSemicolons = substr_count($firstLine, ';');
                            $numTabs = substr_count($firstLine, "\t");
                            if ($numSemicolons > $numCommas && $numSemicolons > $numTabs) {
                                $delimiter = ';';
                            } elseif ($numTabs > $numCommas && $numSemicolons === $numTabs) {
                                $delimiter = "\t";
                            }
                        }
                        $reader->setDelimiter($delimiter);
                        $spreadsheet = $reader->load($filePath);
                    } catch (\Exception $e) {
                        \Log::error('CSV Load Error: ' . $e->getMessage());
                        \Filament\Notifications\Notification::make()
                            ->title(app()->getLocale() == 'ar' ? 'خطأ في قراءة ملف CSV' : 'Error Reading CSV')
                            ->danger()
                            ->send();
                        @unlink($filePath);
                        return;
                    }
                } else {
                    try {
                        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
                    } catch (\Exception $e) {
                        \Log::error('Excel Load Error: ' . $e->getMessage());
                        \Filament\Notifications\Notification::make()
                            ->title(app()->getLocale() == 'ar' ? 'تنسيق ملف غير صالح' : 'Invalid File Format')
                            ->body(app()->getLocale() == 'ar' 
                                ? 'فشل قراءة الملف كـ Excel أو CSV. يرجى التأكد من سلامة وصيغة الملف.' 
                                : 'Failed to read file as Excel or CSV. Please ensure the file is valid.')
                            ->danger()
                            ->send();
                        @unlink($filePath);
                        return;
                    }
                }

                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray(null, true, true, false);

                if (empty($rows)) {
                    \Filament\Notifications\Notification::make()
                        ->title(app()->getLocale() == 'ar' ? 'ملف فارغ أو غير صالح' : 'Empty or Invalid File')
                        ->danger()
                        ->send();
                    @unlink($filePath);
                    return;
                }

                $headers = array_shift($rows);
                if (empty($headers)) {
                    \Filament\Notifications\Notification::make()
                        ->title(app()->getLocale() == 'ar' ? 'ملف فارغ أو غير صالح' : 'Empty or Invalid File')
                        ->danger()
                        ->send();
                    @unlink($filePath);
                    return;
                }

                // Clean headers
                $headers = array_map(function($header) {
                    return trim(str_replace(['"', "'"], '', $header));
                }, $headers);

                $successCount = 0;
                $errorCount = 0;
                $rowIndex = 1;

                foreach ($rows as $row) {
                    $nonEmptyCells = array_filter($row, function($cell) {
                        return $cell !== null && $cell !== '';
                    });
                    if (empty($nonEmptyCells)) {
                        continue;
                    }

                    if (count($row) < count($headers)) {
                        $row = array_pad($row, count($headers), '');
                    }
                    $rowData = array_combine($headers, array_slice($row, 0, count($headers)));
                    
                    try {
                        $importCallback($rowData);
                        $successCount++;
                    } catch (\Exception $e) {
                        \Log::error('Import error at row ' . $rowIndex . ': ' . $e->getMessage());
                        $errorCount++;
                    }
                    $rowIndex++;
                }

                @unlink($filePath);

                $successTitle = app()->getLocale() == 'ar' ? 'اكتمل الاستيراد' : 'Import Completed';
                $successBody = app()->getLocale() == 'ar' 
                    ? "تم استيراد {$successCount} سجل بنجاح. فشل {$errorCount} سجل." 
                    : "Successfully imported {$successCount} records. Failed {$errorCount} records.";

                \Filament\Notifications\Notification::make()
                    ->title($successTitle)
                    ->body($successBody)
                    ->success()
                    ->send();
            });
    }
}
