<?php

namespace App\Providers;

use App\Models\Offer;
use App\Models\Order;
use App\Models\Booking;
use App\Observers\OfferObserver;
use App\Observers\OrderObserver;
use App\Observers\BookingObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });

        \BezhanSalleh\LanguageSwitch\LanguageSwitch::configureUsing(function (\BezhanSalleh\LanguageSwitch\LanguageSwitch $switch) {
            $switch
                ->locales(['ar', 'en']);
        });

        // Register OfferObserver
        Offer::observe(OfferObserver::class);

        // Register OrderObserver
        Order::observe(OrderObserver::class);

        // Register BookingObserver
        Booking::observe(BookingObserver::class);
    }
}
