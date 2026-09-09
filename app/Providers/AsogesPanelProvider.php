<?php

namespace App\Providers;

use App\Pages\SelectAso;
use App\Pages\Dashboard;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use App\Http\Middleware\SelectAsoMiddleware;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Auth\MultiFactor\App\AppAuthentication;

class AsogesPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('asoges')
            ->path('')
            ->homeUrl('/')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->topNavigation(fn () => ! request()->routeIs('filament.asoges.pages.select-aso') && filled(session('rol_activo')))
            ->navigation(fn () => ! request()->routeIs('filament.asoges.pages.select-aso') && filled(session('rol_activo')))
            ->navigationGroups([
                'Libros',
                'CRM',
                'Maestros',
            ])
            ->discoverResources(in: app_path('Resources'), for: 'App\\Resources')
            ->discoverPages(in: app_path('Pages'), for: 'App\\Pages')
            ->pages([
                SelectAso::class,
                Dashboard::class
            ])
            ->discoverWidgets(in: app_path('Widgets'), for: 'App\\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->authGuard('web')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SelectAsoMiddleware::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->profile()
            ->multiFactorAuthentication([
                AppAuthentication::make()
                    ->recoverable()
                    ->recoveryCodeCount(8)
            ], isRequired: true);
    }
}