<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;

class BookingsChart extends ChartWidget
{
    protected static ?int $sort = -3;

    public function getHeading(): string
    {
        return app()->getLocale() === 'ar' ? 'مخطط الحجوزات الشهري' : 'Monthly Bookings Chart';
    }

    protected function getData(): array
    {
        $isAr = app()->getLocale() === 'ar';

        // Fetch bookings count grouped by month for the current year
        $data = collect(range(1, 12))->map(function ($month) {
            return Booking::whereYear('created_at', date('Y'))
                ->whereMonth('created_at', $month)
                ->count();
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => $isAr ? 'الحجوزات' : 'Bookings',
                    'data' => $data,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
                    'fill' => true,
                ],
            ],
            'labels' => $isAr 
                ? ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر']
                : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
