<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\PovertyOverviewStats;
use App\Filament\Widgets\PovertyTrendForecastChart;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
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
            ->brandName('SIPP · Cálculo y Proyección de Pobreza')
            ->login()
            ->colors([
                'primary' => Color::Teal,
            ])
            ->navigationItems([
                NavigationItem::make('Calculadora Pública (Tipo IMC)')
                    ->url('/calculadora')
                    ->icon(Heroicon::OutlinedCalculator)
                    ->group('Portal Frontend')
                    ->sort(1),
                NavigationItem::make('Análisis Temporal y Pronósticos')
                    ->url('/analisis-temporal')
                    ->icon(Heroicon::OutlinedPresentationChartLine)
                    ->group('Portal Frontend')
                    ->sort(2),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                PovertyOverviewStats::class,
                PovertyTrendForecastChart::class,
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
            ]);
    }
}
