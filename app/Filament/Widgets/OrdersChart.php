<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected static ?int $sort = -4;

    public function getHeading(): string
    {
        return app()->getLocale() === 'ar' ? 'مخطط الطلبات الشهري' : 'Monthly Orders Chart';
    }

    protected function getData(): array
    {
        $isAr = app()->getLocale() === 'ar';

        // Fetch orders count grouped by month for the current year
        $data = collect(range(1, 12))->map(function ($month) {
            return Order::whereYear('created_at', date('Y'))
                ->whereMonth('created_at', $month)
                ->count();
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => $isAr ? 'الطلبات' : 'Orders',
                    'data' => $data,
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.2)',
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
        return 'line';
    }
}
