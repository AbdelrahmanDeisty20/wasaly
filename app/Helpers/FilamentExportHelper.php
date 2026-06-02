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
        $labelAr = 'استيراد من إكسيل';
        $labelEn = 'Import from Excel';
        $label = app()->getLocale() == 'ar' ? $labelAr : $labelEn;

        return Action::make('import_excel')
            ->label($label)
            ->icon('heroicon-o-document-arrow-up')
            ->color('info')
            ->form([
                FileUpload::make('file')
                    ->label(app()->getLocale() == 'ar' ? 'ملف CSV / Excel (ترميز عربي UTF-8)' : 'CSV / Excel File')
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

                $handle = fopen($filePath, 'r');
                
                // Skip BOM if present
                $bom = fread($handle, 3);
                if ($bom !== "\xEF\xBB\xBF") {
                    rewind($handle);
                }

                $headers = fgetcsv($handle, 0, ',');
                if (!$headers) {
                    rewind($handle);
                    if ($bom === "\xEF\xBB\xBF") {
                        fread($handle, 3);
                    }
                    $headers = fgetcsv($handle, 0, ';');
                }

                if (!$headers) {
                    \Filament\Notifications\Notification::make()
                        ->title(app()->getLocale() == 'ar' ? 'ملف فارغ أو غير صالح' : 'Empty or Invalid File')
                        ->danger()
                        ->send();
                    fclose($handle);
                    return;
                }

                // Clean headers
                $headers = array_map(function($header) {
                    return trim(str_replace(['"', "'"], '', $header));
                }, $headers);

                $successCount = 0;
                $errorCount = 0;
                $rowIndex = 1;

                while (($row = fgetcsv($handle, 0, ',')) !== false || ($row = fgetcsv($handle, 0, ';')) !== false) {
                    // Skip empty rows
                    if (empty($row) || (count($row) === 1 && $row[0] === null)) {
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

                fclose($handle);
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
