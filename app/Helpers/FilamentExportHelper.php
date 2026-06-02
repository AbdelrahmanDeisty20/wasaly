<?php

namespace App\Helpers;

use Filament\Actions\BulkAction;
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
}
