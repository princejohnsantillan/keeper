<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Panels\Guardian\Pages\Register;
use App\Http\Middleware\RedirectGuardianDashboard;
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

final class GuardianPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('guardian')
            ->path('dashboard')
            ->registration(Register::class)
            ->login()
            ->passwordReset()
            ->emailVerification()
            ->emailChangeVerification()
            ->profile()
            ->revealablePasswords(false)
            ->viteTheme('resources/css/filament/guardian/theme.css')
            ->colors([
                'danger' => Color::Red,
                'gray' => Color::Stone,
                'info' => Color::Sky,
                'primary' => Color::Lime,
                'success' => Color::Green,
                'warning' => Color::Orange,
            ])
            ->breadcrumbs(false)
            ->sidebarWidth('14rem')
            ->discoverResources(in: app_path('Filament/Panels/Guardian/Resources'), for: 'App\Filament\Panels\Guardian\Resources')
            ->discoverPages(in: app_path('Filament/Panels/Guardian/Pages'), for: 'App\Filament\Panels\Guardian\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Panels/Guardian/Widgets'), for: 'App\Filament\Panels\Guardian\Widgets')
            ->middleware([
                RedirectGuardianDashboard::class,
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
