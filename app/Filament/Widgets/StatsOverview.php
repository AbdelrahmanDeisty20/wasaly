<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Order;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Service;
use App\Models\User;
use App\Models\Contact;
use App\Models\Offer;
use App\Models\Coupon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = -5;

    protected function getStats(): array
    {
        $isAr = app()->getLocale() === 'ar';

        // 1. Sales & Revenue
        $totalSales = Order::where('status', 'delivered')->sum('total_price') ?? 0;
        $todaySales = Order::where('status', 'delivered')->whereDate('created_at', today())->sum('total_price') ?? 0;
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();

        // 2. Bookings
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();

        // 3. User Accounts
        $customersCount = User::where('type', 'user')->count();
        $providersCount = User::where('type', 'service_provider')->count();

        // 4. Content & Marketing
        $productsCount = Product::count();
        $servicesCount = Service::count();
        $activeOffers = Offer::count();
        $activeCoupons = Coupon::where('is_active', true)->count();

        // 5. Reviews & Support
        $totalReviews = \App\Models\Review::count();
        $averageRating = number_format(\App\Models\Review::avg('rating') ?? 0, 1);
        $pendingContacts = Contact::where('is_read', false)->count();

        return [
            // Sales Stat
            Stat::make(
                $isAr ? 'إجمالي المبيعات المكتملة' : 'Total Completed Sales',
                number_format($totalSales, 2) . ' EGP'
            )
                ->description($isAr ? 'الأرباح من الطلبات التي تم تسليمها' : 'Revenue from delivered orders')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([$totalSales * 0.2, $totalSales * 0.5, $totalSales * 0.8, $totalSales])
                ->color('success')
                ->url(\App\Filament\Resources\Orders\OrderResource::getUrl('index')),

            // Today's Sales Stat
            Stat::make(
                $isAr ? 'مبيعات اليوم' : 'Today\'s Sales',
                number_format($todaySales, 2) . ' EGP'
            )
                ->description($isAr ? 'الأرباح المحققة اليوم' : 'Revenue generated today')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->url(\App\Filament\Resources\Orders\OrderResource::getUrl('index')),

            // Orders Stat
            Stat::make(
                $isAr ? 'إجمالي الطلبات' : 'Total Orders',
                $totalOrders
            )
                ->description($isAr ? "{$pendingOrders} طلبات قيد الانتظار" : "{$pendingOrders} pending orders")
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->chart([5, 10, 8, 15, $totalOrders])
                ->color('info')
                ->url(\App\Filament\Resources\Orders\OrderResource::getUrl('index')),

            // Bookings Stat
            Stat::make(
                $isAr ? 'إجمالي الحجوزات' : 'Total Bookings',
                $totalBookings
            )
                ->description($isAr ? "{$pendingBookings} حجوزات معلقة" : "{$pendingBookings} pending bookings")
                ->descriptionIcon('heroicon-m-calendar-days')
                ->chart([3, 7, 5, 12, $totalBookings])
                ->color('warning')
                ->url(\App\Filament\Resources\Bookings\BookingResource::getUrl('index')),

            // Customers Stat
            Stat::make(
                $isAr ? 'العملاء المسجلين' : 'Registered Customers',
                $customersCount
            )
                ->description($isAr ? 'المستخدمين النشطين بالتطبيق' : 'Active users on the app')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->url(\App\Filament\Resources\Users\UserResource::getUrl('index')),

            // Service Providers Stat
            Stat::make(
                $isAr ? 'مقدمي الخدمات' : 'Service Providers',
                $providersCount
            )
                ->description($isAr ? 'حسابات مقدمي الخدمات المسجلة' : 'Registered service provider accounts')
                ->descriptionIcon('heroicon-m-identification')
                ->color('success')
                ->url(\App\Filament\Resources\Providers\ProviderResource::getUrl('index')),

            // Products & Services Stat
            Stat::make(
                $isAr ? 'المعروضات' : 'Catalog Items',
                ($productsCount + $servicesCount)
            )
                ->description($isAr ? "{$productsCount} منتج | {$servicesCount} خدمة" : "{$productsCount} Products | {$servicesCount} Services")
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('gray')
                ->url(\App\Filament\Resources\Products\ProductResource::getUrl('index')),

            // Active Offers Stat
            Stat::make(
                $isAr ? 'العروض النشطة' : 'Active Offers',
                $activeOffers
            )
                ->description($isAr ? 'العروض الترويجية الحالية' : 'Current promotional offers')
                ->descriptionIcon('heroicon-m-fire')
                ->color('danger')
                ->url(\App\Filament\Resources\Offers\OfferResource::getUrl('index')),

            // Active Coupons Stat
            Stat::make(
                $isAr ? 'الكوبونات الفعالة' : 'Active Coupons',
                $activeCoupons
            )
                ->description($isAr ? 'كوبونات خصم متاحة للاستخدام' : 'Discount coupons available for use')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('success')
                ->url(\App\Filament\Resources\Coupons\CouponResource::getUrl('index')),

            // Reviews Stat
            Stat::make(
                $isAr ? 'متوسط التقييمات' : 'Average Rating',
                "{$averageRating} / 5.0"
            )
                ->description($isAr ? "إجمالي {$totalReviews} تقييم من العملاء" : "Total of {$totalReviews} customer reviews")
                ->descriptionIcon('heroicon-m-star')
                ->color('warning')
                ->url(\App\Filament\Resources\Reviews\ReviewResource::getUrl('index')),

            // Unread Support Messages Stat
            Stat::make(
                $isAr ? 'رسائل الدعم المعلقة' : 'Pending Support Messages',
                $pendingContacts
            )
                ->description($isAr ? 'رسائل جديدة تطلب الرد والمتابعة' : 'New support messages requiring follow-up')
                ->descriptionIcon('heroicon-m-envelope')
                ->color($pendingContacts > 0 ? 'danger' : 'success')
                ->url(\App\Filament\Resources\Contacts\ContactResource::getUrl('index')),
        ];
    }
}
