<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->databaseNotifications()
            ->colors([
                'primary' => Color::Indigo,
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->brandName(__('messages.welcome') . ' - Wasaly')
            ->brandLogo(asset('settings/logo.png'))
            ->brandLogoHeight('4.5rem')
            ->favicon(asset('settings/favicon.png'))
            ->renderHook('panels::head.end', fn () => new \Illuminate\Support\HtmlString('
                <style>
                    /* Make logo container perfectly circular */
                    .fi-logo {
                        display: flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                    }
                    .fi-logo img {
                        width: 4.5rem !important;
                        height: 4.5rem !important;
                        border-radius: 50% !important;
                        object-fit: cover !important;
                        clip-path: circle(50%) !important;
                        box-shadow: 0 0 0 3px rgba(99,102,241,0.3), 0 4px 12px rgba(0,0,0,0.2);
                        display: block !important;
                    }
                    .fi-sidebar-header .fi-logo img {
                        width: 3.2rem !important;
                        height: 3.2rem !important;
                        border-radius: 50% !important;
                        object-fit: cover !important;
                        clip-path: circle(50%) !important;
                    }
                    /* Login page logo */
                    .fi-simple-main .fi-logo img {
                        width: 5rem !important;
                        height: 5rem !important;
                        border-radius: 50% !important;
                        clip-path: circle(50%) !important;
                        box-shadow: 0 0 0 4px rgba(99,102,241,0.3), 0 4px 20px rgba(0,0,0,0.3);
                    }
                </style>
            '))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
            ]);
    }
}
