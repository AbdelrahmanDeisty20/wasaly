<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Order;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Service;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $isAr = app()->getLocale() === 'ar';

        // 1. Sales & Revenue
        $totalSales = Order::where('status', 'delivered')->sum('total_price') ?? 0;
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();

        // 2. Bookings
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();

        // 3. User Accounts
        $customersCount = User::where('type', 'user')->count();
        $providersCount = User::where('type', 'service_provider')->count();

        // 4. Content
        $productsCount = Product::count();
        $servicesCount = Service::count();

        return [
            // Sales Stat
            Stat::make(
                $isAr ? 'إجمالي المبيعات المكتملة' : 'Total Completed Sales',
                number_format($totalSales, 2) . ' EGP'
            )
                ->description($isAr ? 'الأرباح من الطلبات التي تم تسليمها' : 'Revenue from delivered orders')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([$totalSales * 0.2, $totalSales * 0.5, $totalSales * 0.8, $totalSales])
                ->color('success'),

            // Orders Stat
            Stat::make(
                $isAr ? 'إجمالي الطلبات' : 'Total Orders',
                $totalOrders
            )
                ->description($isAr ? "{$pendingOrders} طلبات قيد الانتظار" : "{$pendingOrders} pending orders")
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->chart([5, 10, 8, 15, $totalOrders])
                ->color('info'),

            // Bookings Stat
            Stat::make(
                $isAr ? 'إجمالي الحجوزات' : 'Total Bookings',
                $totalBookings
            )
                ->description($isAr ? "{$pendingBookings} حجوزات معلقة" : "{$pendingBookings} pending bookings")
                ->descriptionIcon('heroicon-m-calendar-days')
                ->chart([3, 7, 5, 12, $totalBookings])
                ->color('warning'),

            // Customers Stat
            Stat::make(
                $isAr ? 'العملاء المسجلين' : 'Registered Customers',
                $customersCount
            )
                ->description($isAr ? 'المستخدمين النشطين بالتطبيق' : 'Active users on the app')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            // Service Providers Stat
            Stat::make(
                $isAr ? 'مقدمي الخدمات' : 'Service Providers',
                $providersCount
            )
                ->description($isAr ? 'حسابات مقدمي الخدمات المسجلة' : 'Registered service provider accounts')
                ->descriptionIcon('heroicon-m-identification')
                ->color('success'),

            // Products & Services Stat
            Stat::make(
                $isAr ? 'المعروضات' : 'Catalog Items',
                ($productsCount + $servicesCount)
            )
                ->description($isAr ? "{$productsCount} منتج | {$servicesCount} خدمة" : "{$productsCount} Products | {$servicesCount} Services")
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('gray'),
        ];
    }
}
